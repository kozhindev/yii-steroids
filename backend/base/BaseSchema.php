<?php

namespace steroids\base;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\helpers\ArrayHelper;

class BaseSchema extends FormModel
{
    /**
     * @var FormModel|Model
     */
    public $model;

    /**
     * @inheritdoc
     */
    public function toFrontend($fields = null)
    {
        $fields = $fields ? (array)$fields : ['*'];

        $result = [];
        foreach ($fields as $key => $name) {
            if (is_int($key) && is_string($name)) {
                // Check getter and property
                if ($this->canGetProperty($name, true, false)) {
                    $result[$name] = $this->$name;
                } elseif ($this->model) {
                    $result[$key] = static::anyToFrontend(ArrayHelper::getValue($this->model, $name));
                }
            } else {
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($this, $name));
            }
        }
        return $result;
    }
}
