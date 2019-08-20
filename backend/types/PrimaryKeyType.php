<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class PrimaryKeyType extends Type
{
    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'InputField',
                'attribute' => $attribute,
                'type' => 'hidden',
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function prepareSearchFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'NumberField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @param string $modelClass
     * @param string $attribute
     * @param string $property
     */
    public function prepareSwaggerProperty($modelClass, $attribute, &$property)
    {
        $property = array_merge(
            [
                'type' => 'number',
            ],
            $property
        );
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_PK;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return false;
    }
}
