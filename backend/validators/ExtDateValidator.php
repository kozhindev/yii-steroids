<?php

namespace steroids\validators;

use yii\validators\StringValidator;

class ExtDateValidator extends StringValidator
{
    public function init()
    {
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} must be a date.');
        }
        parent::init();
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $int = strtotime($model->$attribute);

        // Validate
        if ($int === false) {
            $this->addError($model, $attribute, $this->message);
        } else {

            // Normalize for DBMS
            $value = date('Y-m-d', $int);
            if ($value !== $model->$attribute) {
                $model->$attribute = $value;
            }
        }
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    protected function validateValue($value)
    {
        return strtotime($value) !== false ? null : [$this->message, []];
    }
}
