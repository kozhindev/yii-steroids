<?php

namespace steroids\types;

use steroids\base\Type;
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
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        // TODO Phone validator
        return [
            [$attributeEntity->name, 'string', 'max' => $attributeEntity->stringLength ?: 255],
        ];
    }
}