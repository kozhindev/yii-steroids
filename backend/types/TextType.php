<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class TextType extends Type
{
    public $formatter = 'ntext';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'TextField',
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
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string'],
        ];
    }
}