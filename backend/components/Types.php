<?php

namespace steroids\components;

use steroids\base\Model;
use steroids\base\Type;
use steroids\types\AutoTimeType;
use steroids\types\BooleanType;
use steroids\types\DoubleType;
use steroids\types\MoneyType;
use steroids\types\CustomType;
use steroids\types\DateTimeType;
use steroids\types\DateType;
use steroids\types\EnumType;
use steroids\types\FilesType;
use steroids\types\FileType;
use steroids\types\HtmlType;
use steroids\types\IntegerType;
use steroids\types\PrimaryKeyType;
use steroids\types\RangeType;
use steroids\types\RelationType;
use steroids\types\SizeType;
use steroids\types\StringType;
use steroids\types\TextType;
use steroids\widgets\FrontendField;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property-read AutoTimeType $autoTime
 * @property-read BooleanType $boolean
 * @property-read MoneyType $money
 * @property-read CustomType $custom
 * @property-read DateTimeType $dateTime
 * @property-read DateType $date
 * @property-read DoubleType $double
 * @property-read EnumType $enum
 * @property-read FilesType $files
 * @property-read FileType $file
 * @property-read HtmlType $html
 * @property-read IntegerType $integer
 * @property-read PrimaryKeyType $primaryKey
 * @property-read RangeType $range
 * @property-read RelationType $relation
 * @property-read SizeType $size
 * @property-read StringType $string
 * @property-read TextType $text
 */
class Types extends Component
{
    /**
     * @var Type[]
     */
    public $types = [];

    public function init()
    {
        parent::init();
        $this->types = array_merge($this->getDefaultTypes(), $this->types);
    }

    public function __get($name)
    {
        if (isset($this->types[$name])) {
            return $this->getType($name);
        }

        return parent::__get($name);
    }

    /**
     * @param string $name
     * @return Type|null
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            return null;
        }

        if (is_array($this->types[$name]) || is_string($this->types[$name])) {
            $this->types[$name] = \Yii::createObject($this->types[$name]);
            $this->types[$name]->name = $name;
        }
        return $this->types[$name];
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array|null $field
     * @param array $options
     * @return object|string
     */
    public function renderField($model, $attribute, $field = null, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute) ?: [];

        $type = $this->getTypeByItem($item);
        $config = [
            'class' => FrontendField::className(),
            'model' => $model,
            'attribute' => $attribute,
            'field' => $field,
            'options' => $options,
        ];
        if (is_string($type->inputWidget)) {
            $config['class'] = $type->inputWidget;
        } elseif (is_array($type->inputWidget)) {
            $config = array_merge($config, $type->inputWidget);
        }

        if (property_exists($config['class'], 'metaItem')) {
            $config['metaItem'] = $item;
        }

        /** @var \yii\base\Widget $class */
        $class = $config['class'];
        unset($config['class']);

        $value = $type->renderInputWidget($item, $class, $config);
        if ($value !== null) {
            return $value;
        }

        return $class::widget($config);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderValue($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        if (!$item) {
            return '';
        }

        $type = $this->getTypeByItem($item);
        $value = $type->renderValue($model, $attribute, $item, $options);
        if ($value !== null) {
            return $value;
        }
        if (is_callable($type->formatter)) {
            return call_user_func($type->formatter, $model->$attribute, $model, $attribute, $item, $options);
        } elseif (is_array($type->formatter) || is_string($type->formatter)) {
            return \Yii::$app->formatter->format($model->$attribute, $type->formatter);
        }

        return Html::encode($model->$attribute);
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return array_map(function ($name) {
            return $this->getType($name);
        }, array_keys($this->types));
    }

    /**
     * @param array $item
     * @return Type|null
     * @throws Exception
     */
    protected function getTypeByItem($item)
    {
        $appType = !empty($item['appType']) ? $item['appType'] : 'string';
        $component = $this->getType($appType);
        if (!$component) {
            throw new Exception('Not found app type `' . $appType . '`');
        }

        return $component;
    }

    /**
     * @param Model $modelClass
     * @param string $attribute
     * @return array|null
     */
    protected function getMetaItem($modelClass, $attribute)
    {
        if (is_object($modelClass)) {
            $modelClass = get_class($modelClass);
        }

        $meta = $modelClass::meta();
        $attribute = Html::getAttributeName($attribute);

        return isset($meta[$attribute]) ? $meta[$attribute] : null;
    }

    protected function getDefaultTypes()
    {
        $types = [];
        foreach (scandir(__DIR__ . '/../types') as $file) {
            $name = preg_replace('/\.php$/', '', $file);
            $id = lcfirst(preg_replace('/Type$/', '', $name));
            $class = '\steroids\types\\' . $name;
            if (class_exists($class)) {
                $types[$id] = [
                    'class' => $class,
                ];
            }
        }
        return $types;
    }
}