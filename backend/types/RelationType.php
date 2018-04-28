<?php

namespace steroids\types;

use steroids\base\Model;
use steroids\base\Type;
use steroids\modules\gii\models\ModelMetaClass;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

class RelationType extends Type
{
    const OPTION_RELATION_NAME = 'relationName';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'DropDownField',
                'attribute' => $attribute,
                'autoComplete' => true,
                'dataProvider' => [
                    'action' => '', // TODO
                ],
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderInputWidget($item, $class, $config)
    {
        /** @var Model $modelClass */
        $modelClass = $config['model'];
        $relationName = ArrayHelper::getValue($item, self::OPTION_RELATION_NAME);

        $relation = $modelClass->getRelation($relationName);
        $config['options']['multiple'] = $relation && $relation->multiple;

        return $class::widget($config);
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        $relationName = ArrayHelper::remove($item, self::OPTION_RELATION_NAME);
        $models = !is_array($model->$relationName) ? [$model->$relationName] : $model->$relationName;

        return implode(', ', array_map(function ($model) use ($options) {
            /** @type Model $model */
            if (!($model instanceof Model)) {
                return '';
            }

            foreach ($model->getModelLinks(\Yii::$app->user->model) as $url) {
                if (\Yii::$app->siteMap->isAllowAccess($url)) {
                    return Html::a($model->modelLabel, $url, $options);
                }
            }

            return $model->modelLabel;
        }, $models));
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        $relation = $attributeEntity->modelEntity->getRelationEntity($attributeEntity->relationName);
        return $relation && $relation->isHasOne ? Schema::TYPE_INTEGER : false;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        $relation = $attributeEntity->modelEntity->getRelationEntity($attributeEntity->relationName);
        if ($relation && $relation->isHasOne) {
            return [
                [$attributeEntity->name, 'integer'],
            ];
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($attributeEntity)
    {
        return [];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_RELATION_NAME,
                'component' => 'InputField',
                'label' => 'Relation name',
                'list' => 'relations',
            ],
        ];
    }

}