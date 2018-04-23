<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\gii\models\MetaItem;
use yii\helpers\ArrayHelper;

class RangeType extends Type
{
    const OPTION_SUB_APP_TYPE = 'subAppType';
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
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        $subAppType = ArrayHelper::remove($item, self::OPTION_SUB_APP_TYPE);
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
    public function getItems($metaItem)
    {
        if ($metaItem->refAttribute) {
            return [
                new MetaItem([
                    'metaClass' => $metaItem->metaClass,
                    'name' => $metaItem->refAttribute,
                    'appType' => $metaItem->subAppType,
                    'publishToFrontend' => $metaItem->publishToFrontend,
                ]),
            ];
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return \Yii::$app->types->getType($metaItem->subAppType)->giiDbType($metaItem);
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($metaItem)
    {
        return \Yii::$app->types->getType($metaItem->subAppType)->giiBehaviors($metaItem);
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return \Yii::$app->types->getType($metaItem->subAppType)->giiRules($metaItem, $useClasses);
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_SUB_APP_TYPE => [
                'component' => 'input',
                'list' => 'types',
                'style' => [
                    'width' => '90px',
                ],
            ],
            self::OPTION_REF_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'Attribute "to"',
            ],
        ];
    }
}