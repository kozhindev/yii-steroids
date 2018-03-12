<?php

namespace steroids\validators;

use Yii;
use yii\validators\Validator;

class WordsValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if (!is_string($value)) {
            $this->addError($model, $attribute, 'Поле «{attribute}» должно быть строкой');
        } elseif (preg_match('/<[^>]+>/', $value)) {
            $this->addError($model, $attribute, 'Поле «{attribute}» не должно содержать HTML теги');
        }
    }
}