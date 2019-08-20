<?php

namespace steroids\traits;

use steroids\base\BaseSchema;
use steroids\base\FormModel;
use steroids\base\Model;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
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
     * @param Model|Model[]|mixed|null $model
     * @param null $fields
     * @return array|null
     * @throws InvalidConfigException
     */
    public static function anyToFrontend($model, $fields = null)
    {
        $fields = $fields ? (array)$fields : ['*'];

        // Detect array
        if (is_array($model)) {
            return array_map(function($item) use ($fields) {
                return static::anyToFrontend($item, $fields);
            }, $model);
        }

        // Detect empty
        if (!$model) {
            return $model;
        }

        // Scalar
        if (!is_object($model)) {
            return $model;
        }

        // Detect *
        foreach ($fields as $key => $name) {
            // Syntax: *
            if ($name === '*') {
                unset($fields[$key]);
                $fields = array_merge($fields, $model->fields());

                if ($model instanceof BaseSchema && $model->model instanceof Model) {
                    $index = array_search('*', $fields);
                    if ($index !== false) {
                        unset($fields[$index]);
                        $fields = array_merge($fields, $model->model->fields());
                    }
                }
                break;
            }
        }

        $result = [];

        // Detect * => model.*
        foreach ($fields as $key => $name) {
            // Syntax: * => model.*
            if ($key === '*' && preg_match('/\.*$/', $name) !== false) {
                unset($fields[$key]);

                /** @var Model $subModel */
                $attribute = substr($name, 0, -2);
                $subModel = ArrayHelper::getValue($model, $attribute);
                if ($subModel) {
                    foreach ($subModel->fields() as $key => $name) {
                        $key = is_int($key) ? $name : $key;
                        $fields[$key] = $attribute . '.' . $name;
                    }
                    //$result = array_merge($result, static::anyToFrontend($subModel));
                }
            }
        }

        // Export
        foreach ($fields as $key => $name) {
            $key = is_int($key) ? $name : $key;

            if (!is_string($key)) {
                throw new InvalidConfigException('Wrong fields format for model "' . get_class($model) . '"');
            }

            // Detect path
            if (is_string($name) && strpos($name, '.') !== false) {
                $parts = explode('.', $name);
                $name = array_pop($parts);
                $item = ArrayHelper::getValue($model, $parts);
            } else {
                $item = $model;
            }

            // BaseScheme logic
            if (is_string($name) && $item instanceof BaseSchema) {
                if ($item->canGetProperty($name, true, false)) {
                    $result[$key] = static::anyToFrontend($item->$name);
                    continue;
                } else {
                    $item = $item->model;
                }
            }

            // Standard model logic
            if (is_callable($name)) {
                $result[$key] = static::anyToFrontend(call_user_func($name, $item));
            } elseif (is_array($name)) {
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($item, $key), $name);
            } else {
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($item, $name));
            }
        }
        return $result;
    }

    /**
     * Note: when user param is supplied, fields in result will be filtered
     * according to the user permissions.
     *
     * @param array|string|null $fields
     * @param Model $user
     * @return array
     * @throws InvalidConfigException
     */
    public function toFrontend($fields = null, $user = null)
    {
        $data = static::anyToFrontend($this, $fields);
        $model = $this instanceof BaseSchema ? $this->model : $this;

        if ($user && $model instanceof Model) {
            $canView = $model->canView($user);
            if (is_array($canView)) {
                /** @var Model $modelClass */
                $modelClass = get_class($model);
                $notPermittedFields = array_diff(array_keys($modelClass::meta()), $canView);
                if ($notPermittedFields) {
                    $data = array_filter($data,
                        function($attribute) use ($notPermittedFields) {
                            return !in_array($attribute, $notPermittedFields);
                        },
                        ARRAY_FILTER_USE_KEY
                    );
                }
            }
        }

        return $data;
    }
}
