<?php

namespace steroids\traits;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\helpers\ArrayHelper;

trait MetaTrait
{
    /**
     * @return array
     */
    public static function meta()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [];
        foreach (static::meta() as $attribute => $item) {
            if (isset($item['label']) && is_string($item['label'])) {
                $labels[$attribute] = $item['label'];
            }
        }
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $hints = [];
        foreach (static::meta() as $attribute => $item) {
            if (isset($item['hint']) && is_string($item['hint'])) {
                $hints[$attribute] = $item['hint'];
            }
        }
        return $hints;
    }

    /**
     * @param array|null $fields
     * @return array
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

        $entry = [];
        foreach ($fields as $key => $name) {
            if (is_int($key)) {
                $key = $name;
            }

            if (is_array($name)) {
                // Relations
                $relation = $this->getRelation($key, false);
                if ($relation) {
                    if ($relation->multiple) {
                        $entry[$key] = [];
                        foreach ($this->$key as $childModel) {
                            /** @type Model $childModel */
                            $entry[$key][] = $childModel->toFrontend($name);
                        }
                    } else {
                        $entry[$key] = $this->$key ? $this->$key->toFrontend($name) : null;
                    }
                } else {
                    $child = $this->$key;
                    if (is_array($child)) {
                        $entry[$key] = [];
                        foreach ($child as $childModel) {
                            if (is_object($childModel) && method_exists($childModel, 'toFrontend')) {
                                /** @type Model $childModel */
                                $entry[$key][] = $childModel->toFrontend($name);
                            }
                        }
                    } else {
                        $entry[$key] = is_object($child) && method_exists($child, 'toFrontend')
                            ? $child->toFrontend($name)
                            : null;
                    }
                }
            } else {
                // Attributes
                $value = ArrayHelper::getValue($this, $name);
                $key = is_string($key) ? $key : $name;
                if (is_array($value)) {
                    $entry[$key] = [];
                    foreach ($value as $valueItem) {
                        if (is_object($valueItem) && method_exists($valueItem, 'toFrontend')) {
                            /** @type Model $childModel */
                            $entry[$key][] = $valueItem->toFrontend();
                        }
                    }
                } else {
                    $entry[$key] = is_object($value) && method_exists($value, 'toFrontend')
                        ? $value->toFrontend()
                        : $value;
                }
            }
        }
        return $entry;
    }
}