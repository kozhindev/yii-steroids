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
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_DOUBLE;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'number'],
        ];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            self::OPTION_SCALE => [
                'component' => 'input',
                'label' => 'Scale',
            ]
        ];
    }
}