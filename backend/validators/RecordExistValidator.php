<?php

namespace steroids\validators;

use steroids\base\Model;
use yii\validators\ExistValidator;

class RecordExistValidator extends ExistValidator
{
    /**
     * Consider attribute value successfully validated if it is an instance of the given targetClass and this
     * instance was just created
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        if (
            $this->targetClass !== null
            && $model->$attribute instanceof $this->targetClass
            && !empty($model->$attribute->isNewRecord)
        ) {
            return;
        }

        if ($model->isRelationPopulated($attribute)) {
            return;
        }

        parent::validateAttribute($model, $attribute);
    }
}
