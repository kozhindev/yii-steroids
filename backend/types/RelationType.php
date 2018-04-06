<?php

namespace steroids\types;

use steroids\base\Model;
use steroids\base\Type;
use steroids\modules\gii\models\ModelMetaClass;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use arogachev\ManyToMany\behaviors\ManyToManyBehavior;
use yii\helpers\Html;
use yii\web\JsExpression;

class RelationType extends Type
{
    const OPTION_RELATION_NAME = 'relationName';
    const OPTION_LIST_RELATION_NAME = 'listRelationName';

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'DropDownField',
            'attribute' => $attribute,
            'autoComplete' => true,
            'dataProvider' => [
                'action' => '', // TODO
            ],
        ];
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
    public function giiDbType($metaItem)
    {
        $relation = $metaItem->metaClass instanceof ModelMetaClass
            ? $metaItem->metaClass->getRelation($metaItem->relationName)
            : null;
        return $relation && $relation->isHasOne ? Schema::TYPE_INTEGER : false;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        $relation = $metaItem->metaClass instanceof ModelMetaClass
            ? $metaItem->metaClass->getRelation($metaItem->relationName)
            : null;
        if ($relation && $relation->isHasOne) {
            return [
                [$metaItem->name, 'integer'],
            ];
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($metaItem)
    {
        if ($metaItem->relationName && !$this->giiDbType($metaItem)) {
            return [
                [
                    'class' => ManyToManyBehavior::className(),
                    'relations' => [
                        [
                            'name' => $metaItem->relationName,
                            'editableAttribute' => $metaItem->name,
                            'autoFill' => false,
                        ]
                    ]
                ],
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            self::OPTION_RELATION_NAME => [
                'component' => 'input',
                'label' => 'Relation name',
                'list' => 'relations',
                'style' => [
                    'width' => '120px',
                ],
            ],
            self::OPTION_LIST_RELATION_NAME => [
                'component' => 'input',
                'label' => 'List relation name',
                'list' => 'relations',
                'style' => [
                    'width' => '120px',
                ],
            ]
        ];
    }

}