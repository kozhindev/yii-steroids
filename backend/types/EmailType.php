<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\gii\models\MetaItem;
use yii\db\Schema;

class EmailType extends Type
{
    public $formatter = 'email';

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'InputField',
            'attribute' => $attribute,
            'type' => 'email',
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
        return [
            [$metaItem->name, 'string', 'max' => 255],
            [$metaItem->name, 'email'],
        ];
    }
}