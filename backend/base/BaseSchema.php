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

        // Detect *
        foreach ($fields as $key => $name) {
            if ($name === '*') {
                unset($fields[$key]);
                $fields = array_merge($fields, $this->fields());
                break;
            }
        }

        $result = [];
        foreach ($fields as $key => $name) {
            if (is_int($key) && is_string($name)) {
                // Check getter and property
                if ($this->canGetProperty($name, true, false)) {
                    $result[$name] = $this->$name;
                } elseif ($this->model) {
                    $result[$name] = static::anyToFrontend(ArrayHelper::getValue($this->model, $name));
                }
            } else {
                if (is_int($key)) {
                    $key = $name;
                }
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($this, $name));
            }
        }
        return $result;
    }
}
