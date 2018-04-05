<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class IntegerType extends Type
{
    public $formatter = 'integer';

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'NumberField',
            'attribute' => $attribute,
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'integer'],
        ];
    }

}