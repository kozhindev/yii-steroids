<?php


namespace steroids\validators;


use yii\validators\BooleanValidator;

class ExtBooleanValidator extends BooleanValidator
{
    public $trueValues = [true, 1, 'true', '1', 'y', 'yes', 'д', 'да'];

    public $falseValues = [false, 0, 'false', '0', 'n', 'no', 'н', 'нет', null];

    public $strict = true;

    public $filter = true;

    public $skipOnEmpty = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} must be one of "{true}" or "{false}".');
        }
    }


    public function validateValue($value)
    {
        $value = is_string($value) ? mb_strtolower($value) : $value;
        $valid = in_array($value, $this->trueValues, $this->strict) || in_array($value, $this->falseValues, $this->strict);

        if (!$valid) {
            return [$this->message, [
                'true' => implode(', ', array_unique($this->trueValues)),
                'false' => implode(', ', array_unique($this->falseValues)),
            ]];
        }

        return null;
    }

    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        } elseif ($this->filter && !is_bool($model->$attribute)) {
            // Change to true or false if filter
            $model->$attribute = in_array($model->$attribute, $this->trueValues, $this->strict) ? true : false;
        }
    }
}