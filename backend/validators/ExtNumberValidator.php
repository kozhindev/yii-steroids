<?php

namespace steroids\validators;

use yii\validators\NumberValidator;
use yii\web\JsExpression;

/**
 * Extended NumberValidator.
 *
 * Deletes spaces and single quotes, and replaces commas with dots before passing values to the NumberValidator.
 * Example: converts "' 24  , 345  '    " to "24.345" and passes latter to the parent validator.
 */
class ExtNumberValidator extends NumberValidator
{
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (is_array($value) || is_object($value)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        $model->$attribute = $this->transform($value);
        parent::validateAttribute($model, $attribute);

        if ($model->hasErrors($attribute)) {
            $model->$attribute = $value;
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function transform($value)
    {
        $raw = str_replace([' ', "'", '’'], '', $value);
        $transformed = str_replace(',', '.', $raw);

        return $transformed;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $js = "value = value && value.replace(/[ '’]/g, '').replace(/,/g, '.');";
        $jsExpression = new JsExpression($js) . parent::clientValidateAttribute($model, $attribute, $view);

        return $jsExpression;
    }
}
