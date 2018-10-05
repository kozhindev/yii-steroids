<?php

namespace steroids\traits;

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
        // Detect array
        if (is_array($model)) {
            return array_map(function($item) use ($fields) {
                return static::anyToFrontend($item, $fields);
            }, $model);
        }

        // Scalar
        if (!is_object($model)) {
            return $model;
        }

        $fields = $fields ? (array)$fields : ['*'];

        // Detect empty
        if (!$model) {
            return is_array($model) ? [] : null;
        }

        // Detect single type
        /*if (!($model instanceof Model)) {
            // Detect Yii Object
            if ($model instanceof BaseObject) {
                return $model->toArray($fields);
            }

            return $model;
        }*/

        // Detect *
        foreach ($fields as $key => $name) {
            if ($name === '*') {
                unset($fields[$key]);
                $fields = array_merge($fields, $model->fields());
                break;
            }
        }

        // Export
        $result = [];
        foreach ($fields as $key => $name) {
            $key = is_int($key) ? $name : $key;

            if (!is_string($key)) {
                throw new InvalidConfigException('Wrong fields format for model "' . get_class($model) . '"');
            }

            if (is_callable($name)) {
                $result[$key] = call_user_func($name, $model);
            } elseif (is_array($name)) {
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($model, $key), $name);
            } else {
                $result[$key] = static::anyToFrontend(ArrayHelper::getValue($model, $name));
            }
        }
        return $result;
    }

    /**
     * @param array|string|null $fields
     * @return array
     * @throws InvalidConfigException
     */
    public function toFrontend($fields = null)
    {
        return static::anyToFrontend($this, $fields);
    }
}