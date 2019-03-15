<?php

namespace steroids\types;

use yii\db\Schema;
use yii\helpers\ArrayHelper;

class DateTimeType extends DateType
{
    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'DateTimeField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function prepareSwaggerProperty($modelClass, $attribute, &$property)
    {
        $property = array_merge(
            [
                'type' => 'string',
                'format' => 'date-time',
            ],
            $property
        );
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        $format = ArrayHelper::remove($item, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDatetime($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_DATETIME;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'date', 'format' => $attributeEntity->format ?: 'php:Y-m-d H:i:s'],
        ];
    }
}
