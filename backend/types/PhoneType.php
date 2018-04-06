<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\gii\models\MetaItem;
use yii\db\Schema;

class PhoneType extends Type
{
    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'InputField',
            'attribute' => $attribute,
            'type' => 'phone',
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        // TODO Phone validator
        return [
            [$metaItem->name, 'string', 'max' => $metaItem->stringLength ?: 255],
        ];
    }
}