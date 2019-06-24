<?php

namespace steroids\types;

use steroids\base\Enum;
use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Type;
use steroids\modules\gii\forms\EnumEntity;
use steroids\modules\gii\helpers\GiiHelper;
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
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'DropDownField',
                'attribute' => $attribute,
                'items' => $this->getItemsProperty($modelClass, $attribute),
            ],
            $props
        );
    }

    public function prepareFormatterProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'EnumFormatter',
                'attribute' => $attribute,
                'items' => $this->getItemsProperty($modelClass, $attribute),
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function prepareSwaggerProperty($modelClass, $attribute, &$property)
    {
        /** @var Enum $enumClass */
        $enumClass = ArrayHelper::getValue($this->getOptions($modelClass, $attribute), self::OPTION_CLASS_NAME);

        $property = array_merge(
            [
                'type' => 'string',
                'enum' => $enumClass ? $enumClass::getKeys() : null,
            ],
            $property
        );
    }

    /**
     * @param Model|FormModel|string $modelClass
     * @param string $attribute
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getItemsProperty($modelClass, $attribute)
    {
        /** @var Enum $enumClass */
        $enumClass = ArrayHelper::getValue($this->getOptions($modelClass, $attribute), self::OPTION_CLASS_NAME);
        return $enumClass ? trim(str_replace('\\', '.', $enumClass), '.') : null;
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
    public function getGiiJsMetaItem($attributeEntity, $item, &$import = [])
    {
        $result = parent::getGiiJsMetaItem($attributeEntity, $item, $import);
        $enumClass = $attributeEntity->getCustomProperty(self::OPTION_CLASS_NAME);
        if ($enumClass) {
            $modelEntity = EnumEntity::findOne($enumClass);
            if (file_exists($modelEntity->getMetaJsPath())) {
                $import[] = 'import ' . $modelEntity->name . 'Meta from \'' . str_replace('\\', '/', $modelEntity->getClassName() . 'Meta') . '\';';
                $result['enumClassName'] = new JsExpression($modelEntity->metaClass->name);
            } elseif (file_exists($modelEntity->getPath())) {
                $import[] = 'import ' . $modelEntity->name . ' from \'' . str_replace('\\', '/', $modelEntity->getClassName()) . '\';';
                $result['enumClassName'] = new JsExpression($modelEntity->name);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        /** @var Enum $enumClass */
        $enumClass = $attributeEntity->getCustomProperty(self::OPTION_CLASS_NAME);
        if (!$enumClass) {
            return [
                [$attributeEntity->name, 'string'],
            ];
        }

        $shortClassName = StringHelper::basename($enumClass);
        $useClasses[] = $enumClass;

        return [
            [$attributeEntity->name, 'in', 'range' => new ValueExpression("$shortClassName::getKeys()")],
        ];
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_CLASS_NAME,
                'component' => 'AutoCompleteField',
                'label' => 'Enum Class',
                'items' => ArrayHelper::getColumn(EnumEntity::findAll(), 'className'),
            ]
        ];
    }
}
