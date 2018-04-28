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
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
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
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        // TODO Html validator
        return [
            [$attributeEntity->name, 'string']
        ];
    }
}