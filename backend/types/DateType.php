<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class DateType extends Type
{
    const OPTION_FORMAT = 'format';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'DateField',
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
        $format = ArrayHelper::remove($item, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDate($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_DATE;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_FORMAT => [
                'component' => 'input',
                'label' => 'Format',
                'list' => [
                    'short',
                    'medium',
                    'long',
                    'full'
                ]
            ],
        ];
    }
}