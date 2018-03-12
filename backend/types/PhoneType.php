<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\gii\models\MetaItem;
use yii\db\Schema;

class PhoneType extends Type
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'PhoneField',
            ]
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