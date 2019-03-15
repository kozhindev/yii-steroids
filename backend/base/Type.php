<?php

namespace steroids\base;

use steroids\modules\gii\forms\ModelAttributeEntity;
use yii\base\BaseObject;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

abstract class Type extends BaseObject
{
    /**
     * @var string
     */
    public $name;
    public $formatter;

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string|null
     */
    public function renderValue($model, $attribute, $item, $options)
    {
        return null;
    }


    /**
     * @param Model|FormModel|string $modelClass
     * @param string $attribute
     * @param array $props
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
    }

    /**
     * @param array $item
     * @param array $params
     * @return array
     */
    public function getFieldData($item, $params)
    {
        return [];
    }

    /**
     * @param Model|FormModel|string $modelClass
     * @param string $attribute
     * @param array $props
     */
    public function prepareFormatterProps($modelClass, $attribute, &$props)
    {
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $values
     */
    public function prepareViewValue($model, $attribute, &$values)
    {
        $values[$attribute] = $model->$attribute;
    }


    /**
     * @param ModelAttributeEntity $attributeEntity
     * @param array $item
     * @return array
     */
    public function getGiiJsMetaItem($attributeEntity, $item, &$import = [])
    {
        return $item;
    }

    /**
     * @param ModelAttributeEntity $attributeEntity
     * @return array
     */
    public function getItems($attributeEntity)
    {
        return [];
    }

    /**
     * @param string $modelClass
     * @param string $attribute
     * @param array $property
     */
    public function prepareSwaggerProperty($modelClass, $attribute, &$property)
    {
        $property = array_merge(
            [
                'type' => 'string',
            ],
            $property
        );
    }

    /**
     * @param ModelAttributeEntity $attributeEntity
     * @param string[] $useClasses
     * @return array
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'string'],
        ];
    }

    /**
     * @param ModelAttributeEntity $attributeEntity
     * @return array
     */
    public function giiBehaviors($attributeEntity)
    {
        return [];
    }

    /**
     * @param ModelAttributeEntity $attributeEntity
     * @return string|false
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [];
    }

    /**
     * @param Model|FormModel|string $modelClass
     * @param string $attribute
     * @return array
     */
    protected function getOptions($modelClass, $attribute)
    {
        if (is_object($modelClass)) {
            $modelClass = get_class($modelClass);
        }

        return ArrayHelper::getValue($modelClass::meta(), $attribute, []);
    }
}
