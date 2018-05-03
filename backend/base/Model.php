<?php

namespace steroids\base;

use steroids\traits\RelationSaveTrait;
use steroids\components\AuthManager;
use steroids\exceptions\ModelDeleteException;
use steroids\exceptions\ModelSaveException;
use steroids\traits\MetaTrait;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
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
     * @param Model[] $models
     * @param array|null $fields
     * @return array
     */
    /*public static function listToFrontend($models, $fields = null)
    {
        $result = [];
        foreach ((array)$models as $model) {
            $result[] = $model->toFrontend($fields);
        }
        return $result;
    }*/

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
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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
        $formName = $formName === null ? $this->formName() : $formName;

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
            throw new InvalidConfigException("Unknown scenario: $scenario");
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

        if ($attribute === null && !empty($this->_listenRelations)) {
            $errors = array_merge($errors, $this->getRelationErrors());
        }

        return $errors;
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
