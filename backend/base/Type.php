<?php

namespace steroids\base;

use steroids\gii\models\MetaItem;
use yii\base\BaseObject;
use yii\db\Schema;
use yii\base\Widget;

abstract class Type extends BaseObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|array
     */
    public $inputWidget;

    /**
     * @var string|array|callable
     */
    public $formatter;

    /**
     * @param array $item
     * @param Widget $class
     * @param array $config
     * @return string
     */
    public function renderInputWidget($item, $class, $config)
    {
        return null;
    }

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
     * @param Model|FormModel $model
     * @param string $attribute
     * @param array $item
     * @return array
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [];
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
}