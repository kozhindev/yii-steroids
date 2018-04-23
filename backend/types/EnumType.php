<?php

namespace steroids\types;

use steroids\base\Enum;
use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Type;
use steroids\modules\gii\models\EnumClass;
use steroids\modules\gii\models\ValueExpression;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

class EnumType extends Type
{
    const OPTION_CLASS_NAME = 'enumClassName';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'DropDownField',
                'attribute' => $attribute,
                'items' => $this->getItemsProp($modelClass, $attribute, $import),
            ],
            $props
        );
    }

    public function prepareFormatterProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'format' => [
                    'component' => 'EnumFormatter',
                    'attribute' => $attribute,
                    'items' => $this->getItemsProp($modelClass, $attribute, $import),
                ],
            ],
            $props
        );
    }

    /**
     * @param Model|FormModel|string $modelClass
     * @param string $attribute
     * @param array|null $import
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getItemsProp($modelClass, $attribute, &$import)
    {
        /** @var Enum $enumClass */
        $enumClass = ArrayHelper::getValue($this->getOptions($modelClass, $attribute), self::OPTION_CLASS_NAME);
        if ($enumClass) {
            if (is_array($import)) {
                $info = (new \ReflectionClass($enumClass))->getParentClass();
                $import[] = "import {$info->getShortName()} from '" . str_replace('\\', '/', $info->getName()) . "';";
                return new JsExpression($info->getShortName());
            } else {
                return $enumClass::toFrontend();
            }
        }
        return null;
    }




    /**
     * @inheritdoc
     */
    public function renderInputWidget($item, $class, $config)
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);
        $config['options']['items'] = $className::getLabels();

        return $class::widget($config);
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);

        $label = $className::getLabel($model->$attribute);
        $cssClass = $className::getCssClass($model->$attribute);

        return $cssClass ? Html::tag('span', $label, ['class' => 'label label-' . $cssClass]) : $label;
    }

    /**
     * @inheritdoc
     */
    public function getGiiJsMetaItem($metaItem, $item, &$import = [])
    {
        $result = parent::getGiiJsMetaItem($metaItem, $item, $import);
        if ($metaItem->enumClassName) {
            $enumClassMeta = EnumClass::findOne($metaItem->enumClassName);
            if (file_exists($enumClassMeta->metaClass->filePath)) {
                $import[] = 'import ' . $enumClassMeta->metaClass->name . ' from \'' . str_replace('\\', '/', $enumClassMeta->metaClass->className) . '\';';
                $result['enumClassName'] = new JsExpression($enumClassMeta->metaClass->name);
            } elseif (file_exists($enumClassMeta->filePath)) {
                $import[] = 'import ' . $enumClassMeta->name . ' from \'' . str_replace('\\', '/', $enumClassMeta->className) . '\';';
                $result['enumClassName'] = new JsExpression($enumClassMeta->name);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;
        if (!$className) {
            return [
                [$metaItem->name, 'string'],
            ];
        }

        $shortClassName = StringHelper::basename($metaItem->enumClassName);
        $useClasses[] = $className;

        return [
            [$metaItem->name, 'in', 'range' => new ValueExpression("$shortClassName::getKeys()")],
        ];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            self::OPTION_CLASS_NAME => [
                'component' => 'input',
                'label' => 'Class',
                'list' => ArrayHelper::getColumn(EnumClass::findAll(), 'className'),
            ]
        ];
    }
}