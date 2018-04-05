<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class HtmlType extends Type
{
    public $formatter = 'raw';

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'HtmlField',
            'attribute' => $attribute,
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        // TODO Html validator
        return [
            [$metaItem->name, 'string']
        ];
    }
}