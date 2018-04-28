<?php

namespace steroids\types;

use steroids\base\Model;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class DoubleType extends IntegerType
{
    const OPTION_SCALE = 'scale';

    public $formatter = null;

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string|null
     */
    public function renderValue($model, $attribute, $item, $options)
    {
        $scale = ArrayHelper::getValue($item, self::OPTION_SCALE) ?: 2;
        return \Yii::$app->formatter->asDecimal($model->$attribute, $scale);
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_DOUBLE;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'number'],
        ];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_SCALE,
                'component' => 'NumberField',
                'label' => 'Scale',
            ]
        ];
    }
}