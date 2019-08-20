<?php

namespace steroids\validators;

use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\validators\Validator;

class PhoneValidator extends Validator
{
    public $countyCode = 7;

    public $enableClientValidation = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = \Yii::t('steroids', 'Неправильный формат телефона, ожидается формат +79001234567');
        }
    }

    /**
     * @param BaseActiveRecord $model the data model to be validated
     * @param string $attribute the name of the attribute to be validated.
     * @throws \Exception
     */
    public function validateAttribute($model, $attribute)
    {
        // Normalize
        if ($model->$attribute) {
            $model->$attribute = $this->getValidatedNumber($model->$attribute);
        }

        if (!preg_match('/^\+' . $this->countyCode . '[0-9]{10}$/', $model->$attribute)) {
            $this->addError($model, $attribute, \Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], \Yii::$app->language));
        }
    }

    public function getValidatedNumber($phoneNumber)
    {
        return '+' . preg_replace('/^8/', $this->countyCode, preg_replace('/[^0-9]/', '', $phoneNumber));
    }

}