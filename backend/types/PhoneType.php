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
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'InputField',
                'attribute' => $attribute,
                'type' => 'phone',
            ],
            $props
        );
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