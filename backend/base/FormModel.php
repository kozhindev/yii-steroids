<?php

namespace steroids\base;

use steroids\traits\MetaTrait;
use steroids\traits\RelationSaveTrait;
use steroids\traits\RelationSimilarTrait;
use steroids\traits\SecurityFieldsTrait;
use yii\base\InvalidConfigException;

class FormModel extends \yii\base\Model
{
    use MetaTrait;
    use RelationSaveTrait;
    use RelationSimilarTrait;
    use SecurityFieldsTrait;

    public function formName()
    {
        return '';
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
}
