<?php

namespace steroids\exceptions;

class ModelSaveException extends ApplicationException
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param \yii\base\Model $model
     */
    public function __construct($model)
    {
        $this->errors = $model->errors;

        parent::__construct('Cannot save model ' . $model->className() . ', errors: ' . print_r($this->errors, true));
    }
}
