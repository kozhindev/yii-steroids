<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class TimeType extends Type
{
    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'InputField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_TIME;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'date', 'format' => 'php:H:i:s'],
        ];
    }
}