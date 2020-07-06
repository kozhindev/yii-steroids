<?php

namespace steroids\traits;

use steroids\base\Model;
use steroids\traits\RelationSaveSubtraits\RelationLoadSubtrait;
use steroids\traits\RelationSaveSubtraits\RelationOrderSubtrait;
use steroids\traits\RelationSaveSubtraits\RelationSaveSubtrait;
use steroids\traits\RelationSaveSubtraits\RelationValidateSubtrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait RelationSaveTrait
{
    use RelationSaveSubtrait;
    use RelationLoadSubtrait;
    use RelationValidateSubtrait;
    use RelationOrderSubtrait;

    private $_listenRelations = [];

    /**
     * @param string|array $relationNames
     */
    public function listenRelationIds($relationNames)
    {
        $relationNames = (array)$relationNames;
        foreach ($relationNames as &$name) {
            // Normalize (remove Ids suffix)
            if (is_string($name)) {
                $name = implode('.', array_map(function ($name) {
                    return $this->getRelationNameByIdsAttribute($name);
                }, explode('.', $name)));
            }
        }

        $this->listenRelation($relationNames, true);

        // Fetch ids from database
        foreach ($relationNames as $path) {
            if (!is_string($path)) {
                continue;
            }

            $names = explode('.', $path);
            $relationName = array_shift($names);
            $model = count($names) > 0 ? ArrayHelper::getValue($this, $names) : $this;
            if ($model && $model instanceof Model) {
                $idsProperty = $this->getIdsAttributeByRelationName($relationName);
                $model->$idsProperty = $model->getRelationIds($relationName);
            }
        }
    }

    /**
     * @param string|array $relationNames
     */
    public function listenRelationData($relationNames)
    {
        $this->listenRelation($relationNames, false);
    }

    protected function listenRelation($relationNames, $isIds = null)
    {
        foreach ((array)$relationNames as $path) {
            if (is_string($path)) {
                $key = str_replace('.', '.children.', $path);

                ArrayHelper::setValue($this->_listenRelations, $key, ArrayHelper::merge(
                    ArrayHelper::getValue($this->_listenRelations, $key, []),
                    [
                        'isIds' => $isIds,
                        'children' => [],
                    ]
                ));
            } elseif (is_array($path)) {
                $this->_listenRelations = ArrayHelper::merge($this->_listenRelations, $relationNames);
                return;
            }
        }
    }

    protected function getIdsAttributeByRelationName($relationName)
    {
        foreach (static::meta() as $attribute => $params) {
            if (ArrayHelper::getValue($params, 'relationName') === $relationName) {
                return $attribute;
            }
        }
        return $relationName . 'Ids';
    }

    protected function getRelationNameByIdsAttribute($idsAttribute)
    {
        return ArrayHelper::getValue(static::meta(), [$idsAttribute, 'relationName'])
            ?: preg_replace('/Ids$/', '', $idsAttribute);
    }

    public function getRelationIds($relationName)
    {
        // Get relation
        /** @var ActiveQuery $relation */
        $relation = $this->getRelation($relationName);

        // Get primary key
        /** @var ActiveRecord $relatedModel */
        $relatedModel = $relation->modelClass;
        $primaryKey = $relatedModel::primaryKey()[0];

        // Fill from database
        $ids = $relation->select($primaryKey)->column();

        // Typecast
        $column = $relatedModel::getTableSchema()->getColumn($primaryKey);
        $relationIds = array_map(
            function ($value) use ($column) {
                return $column->phpTypecast($value);
            },
            $ids
        );

        static::orderIds($relationIds, $relationName, $relation, $primaryKey);

        return $relationIds;
    }
}
