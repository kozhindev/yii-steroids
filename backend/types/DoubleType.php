<?php

namespace steroids\types;

use steroids\base\Model;
use steroids\modules\gii\forms\ModelAttributeEntity;
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
     * @param ModelAttributeEntity $attributeEntity
     * @return string
     */
    public function giiDbType($attributeEntity)
    {
        $scale = ArrayHelper::getValue($attributeEntity->customProperties, self::OPTION_SCALE) ?: 2;
        return (string)\Yii::$app->db->schema->createColumnSchemaBuilder(Schema::TYPE_DECIMAL, [19, $scale]);
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
     * @inheritdoc
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
