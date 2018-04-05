<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class BooleanType extends Type
{
    public $formatter = 'boolean';

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'CheckboxField',
            'attribute' => $attribute,
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_BOOLEAN;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'boolean'],
        ];
    }
}
