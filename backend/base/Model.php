<?php

namespace steroids\base;

use steroids\traits\RelationSaveTrait;
use steroids\components\AuthManager;
use steroids\exceptions\ModelDeleteException;
use steroids\exceptions\ModelSaveException;
use steroids\traits\MetaTrait;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * @property-read string $modelLabel
 */
class Model extends ActiveRecord
{
    use MetaTrait;
    use RelationSaveTrait;

    /**
     * @return string
     */
    public static function getRequestParamName()
    {
        try {
            $pk = static::primaryKey()[0];
        } catch (InvalidConfigException $e) {
            $pk = 'id';
        }
        return lcfirst(substr(strrchr(static::className(), "\\"), 1)) . ucfirst($pk);
    }

    /**
     * @return string
     */
    public function getModelLabel()
    {
        foreach (['title', 'label', 'name'] as $attribute) {
            $label = $this->getAttribute($attribute);
            if ($label) {
                return $label;
            }
        }
        return '#' . $this->primaryKey;
    }

    /**
     * @param Model|null $user
     * @return array
     */
    public function getModelLinks($user)
    {
        return [];
    }


    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @param string|array $condition
     * @return null|static
     * @throws NotFoundHttpException
     */
    public static function findOrPanic($condition)
    {
        $model = static::findOne($condition);
        if (!$model) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        return $model;
    }

    /**
     * @param string[]|null $attributeNames
     * @throws ModelSaveException
     */
    public function saveOrPanic($attributeNames = null)
    {
        if (!$this->save(true, $attributeNames)) {
            throw new ModelSaveException($this);
        }
    }

    /**
     * @throws ModelDeleteException
     */
    public function deleteOrPanic()
    {
        if (!$this->delete()) {
            throw new ModelDeleteException();
        }
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        // Load relations
        $this->loadRelationData($data, $formName);
        $this->loadRelationIds($data, $formName);

        return parent::load($data, $formName);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($clearErrors) {
            $this->clearErrors();
        }

        if (!$this->beforeValidate()) {
            return false;
        }

        // Validate relations
        $this->validateRelationData();

        $scenarios = $this->scenarios();
        $scenario = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidParamException("Unknown scenario: $scenario");
        }

        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        foreach ($this->getActiveValidators() as $validator) {
            $validator->validateAttributes($this, $attributeNames);
        }
        $this->afterValidate();

        return !$this->hasErrors();
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        // Validate
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $this->saveRelationDataBeforeSelf();

            // Save self without validation
            if (!parent::save(false, $attributeNames)) {
                $transaction->rollBack();
                return false;
            }

            $this->saveRelationIds();
            $this->saveRelationDataAfterSelf();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasErrors($attribute = null)
    {
        return !empty($this->getErrors($attribute));
    }

    /**
     * @inheritdoc
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors($attribute);

        // TODO
        if ($attribute === null && !empty($this->_listenRelations)) {
            $errors = array_merge($errors, $this->getRelationErrors());
        }

        return $errors;
    }

    /**
     * @param array|null $fields
     * @return array
     */
    public function toFrontend($fields = null)
    {
        $fields = $fields ?: ['*'];

        // Detect *
        foreach ($fields as $key => $name) {
            if ($name === '*') {
                unset($fields[$key]);
                $fields = array_merge($fields, $this->fields());
                break;
            }
        }

        $entry = [];
        foreach ($fields as $key => $name) {
            if (is_int($key)) {
                $key = $name;
            }

            if (is_array($name)) {
                // Relations
                $relation = $this->getRelation($key, false);
                if ($relation) {
                    if ($relation->multiple) {
                        $entry[$key] = [];
                        foreach ($this->$key as $childModel) {
                            /** @type Model $childModel */
                            $entry[$key][] = $childModel->toFrontend($name);
                        }
                    } else {
                        $entry[$key] = $this->$key ? $this->$key->toFrontend($name) : null;
                    }
                } else {
                    $child = $this->$key;
                    if (is_array($child)) {
                        $entry[$key] = [];
                        foreach ($child as $childModel) {
                            if ($childModel instanceof Model) {
                                /** @type Model $childModel */
                                $entry[$key][] = $childModel->toFrontend($name);
                            }
                        }
                    } else {
                        $entry[$key] = $child instanceof Model ? $child->toFrontend($name) : null;
                    }
                }
            } else {
                // Attributes
                $value = ArrayHelper::getValue($this, $name);
                $entry[is_string($key) ? $key : $name] = $value;
            }
        }
        return $entry;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canView($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, $this, AuthManager::RULE_MODEL_VIEW);
        }
        return $this->canUpdate($user);
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canCreate($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, $this, AuthManager::RULE_MODEL_CREATE);
        }
        return true;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canUpdate($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, $this, AuthManager::RULE_MODEL_UPDATE) && $this->canUpdated();
        }
        return $this->canUpdated();
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canDelete($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, $this, AuthManager::RULE_MODEL_DELETE) && $this->canDeleted();
        }
        return $this->canDeleted();
    }

    /**
     * @return bool
     */
    public function canUpdated()
    {
        return true;
    }

    public function canDeleted()
    {
        return $this->canUpdated() && !$this->isNewRecord;
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert) && $this->canUpdated();
    }

    public function beforeDelete()
    {
        return parent::beforeDelete() && $this->canDeleted();
    }

}
