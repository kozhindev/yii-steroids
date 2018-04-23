<?php

namespace steroids\base;

use steroids\modules\gii\models\MetaItem;
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
     * @param array|null $import
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
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
     * @param array|null $import
     */
    public function prepareFormatterProps($modelClass, $attribute, &$props, &$import = null)
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
     * @param MetaItem $metaItem
     * @param array $item
     * @return array
     */
    public function getGiiJsMetaItem($metaItem, $item, &$import = [])
    {
        return $item;
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getItems($metaItem)
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @param string[] $useClasses
     * @return array
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string'],
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function giiBehaviors($metaItem)
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
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