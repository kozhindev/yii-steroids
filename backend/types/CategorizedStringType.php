<?php

namespace steroids\types;

use steroids\base\Enum;
use yii\helpers\ArrayHelper;

class CategorizedStringType extends EnumType
{
    const OPTION_REF_ATTRIBUTE = 'refAttribute';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'CategorizedStringField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        /** @var Enum $enumClass */
        $enumClass = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);
        $refAttribute = ArrayHelper::getValue($item, self::OPTION_REF_ATTRIBUTE);

        return $model->$attribute . ' ' . $enumClass::getLabel($model->$refAttribute);
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return array_merge(
            parent::giiOptions(),
            [
                self::OPTION_REF_ATTRIBUTE => [
                    'component' => 'input',
                    'label' => 'Category Attribute',
                    'list' => 'attributes',
                    'style' => [
                        'width' => '80px'
                    ]
                ],
            ]
        );
    }
}