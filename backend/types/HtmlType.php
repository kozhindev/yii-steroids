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
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'HtmlField',
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
        // TODO Html validator
        return [
            [$metaItem->name, 'string']
        ];
    }
}