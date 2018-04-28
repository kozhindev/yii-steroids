<?php

namespace steroids\types;

use steroids\base\Type;
use yii\helpers\ArrayHelper;

class RangeType extends Type
{
    const OPTION_RANGE_TYPE = 'rangeType';
    const OPTION_REF_ATTRIBUTE = 'refAttribute';

    const RANGE_POSITION_START = 'start';
    const RANGE_POSITION_END = 'end';

    public $template = '{start} — {end}';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $options = $this->getOptions($modelClass, $attribute);
        $props = array_merge(
            [
                'component' => 'RangeField',
                'attributeFrom' => $attribute,
                'attributeTo' => ArrayHelper::getValue($options, self::OPTION_REF_ATTRIBUTE),
                'type' => ArrayHelper::getValue($options, self::OPTION_RANGE_TYPE),
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        $subAppType = 'string';
        $refAttribute = ArrayHelper::remove($item, self::OPTION_REF_ATTRIBUTE);
        if ($refAttribute) {
            return strtr($this->template, [
                '{start}' => \Yii::$app->types->getType($subAppType)->renderValue($model, $attribute, $item, $options),
                '{end}' => \Yii::$app->types->getType($subAppType)->renderValue($model, $refAttribute, $item, $options),
            ]);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function getItems($attributeEntity)
    {
        if ($attributeEntity->refAttribute) {
            return [
                new MetaItem([
                    'metaClass' => $attributeEntity->metaClass,
                    'name' => $attributeEntity->refAttribute,
                    'appType' => $attributeEntity->subAppType,
                    'publishToFrontend' => $attributeEntity->publishToFrontend,
                ]),
            ];
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return \Yii::$app->types->getType($attributeEntity->subAppType)->giiDbType($attributeEntity);
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($attributeEntity)
    {
        return \Yii::$app->types->getType($attributeEntity->subAppType)->giiBehaviors($attributeEntity);
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return \Yii::$app->types->getType($attributeEntity->subAppType)->giiRules($attributeEntity, $useClasses);
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_RANGE_TYPE,
                'component' => 'DropDownField',
                'items' => [
                    [
                        'id' => 'input',
                        'label' => 'Input',
                    ],
                    [
                        'id' => 'date',
                        'label' => 'Date',
                    ],
                ],
            ],
            [
                'attribute' => self::OPTION_REF_ATTRIBUTE,
                'component' => 'InputField',
                'label' => 'Attribute To',
            ],
        ];
    }
}