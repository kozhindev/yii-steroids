<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\validators\ExtBooleanValidator;
use yii\db\Schema;

class BooleanType extends Type
{
    public $formatter = 'boolean';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'CheckboxField',
                'attribute' => $attribute,
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
                'component' => 'DropDownField',
                'attribute' => $attribute,
                'items' => [
                    [
                        'id' => 1,
                        'label' => \Yii::t('steroids', 'Да')
                    ],
                    [
                        'id' => 0,
                        'label' => \Yii::t('steroids', 'Нет')
                    ],
                ],
                'showReset' => true,
            ],
            $props
        );
    }

    public function prepareFormatterProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'BooleanFormatter',
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
        return Schema::TYPE_BOOLEAN;
    }

    /**
     * @inheritdoc
     */
    public function prepareSwaggerProperty($modelClass, $attribute, &$property)
    {
        $property = array_merge(
            $property,
            [
                'type' => 'boolean',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, ExtBooleanValidator::class],
        ];
    }
}
