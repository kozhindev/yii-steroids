<?php

namespace steroids\base;

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
                    $item = $this->$name;
                    if (is_array($item)) {
                        $result[$name] = [];
                        foreach ($item as $subItem) {
                            $result[$name][] = $subItem instanceof BaseSchema
                                ? $subItem->toFrontend()
                                : static::anyToFrontend($subItem);
                        }
                    } else {
                        $result[$name] = $item instanceof BaseSchema
                            ? $item->toFrontend()
                            : static::anyToFrontend($item);
                    }
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
