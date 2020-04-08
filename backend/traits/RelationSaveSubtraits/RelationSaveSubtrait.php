<?php


namespace steroids\traits\RelationSaveSubtraits;


use steroids\base\Model;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

trait RelationSaveSubtrait
{
    protected function saveRelationIds()
    {
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if (!$isIds) {
                continue;
            }

            $idsProperty = ArrayHelper::getValue($params, 'idsProperty', $this->getIdsAttributeByRelationName($relationName));

            // Get relation and check it
            $relation = $this->getRelation($relationName);
            if (!$relation->multiple) {
                throw new Exception("Try save property '$idsProperty' on hasOne relation '$relationName'");
            }
            if (!$relation->via) {
                throw new Exception("Try save property '$idsProperty' on hasMany relation '$relationName' without junction table (not found calls via() or viaTable() methods in relation)");
            }

            if (is_array($relation->via)) {
                // via
                /* @type ActiveRecord $modelClass */
                $modelClass = $relation->via[1]->modelClass;
                $table = $modelClass::tableName();
                $ownAttribute = key($relation->via[1]->link);
            } else {
                // viaTable
                $table = $relation->via->from[0];
                $ownAttribute = key($relation->via->link);
            }

            $relatedAttribute = reset($relation->link);
            $prevIds = $this->getRelationIds($relationName);
            $nextIds = (array)$this->$idsProperty;

            if (!$this->isNewRecord) {
                // Delete
                static::getDb()->createCommand()->delete($table, [
                    $ownAttribute => $this->primaryKey,
                    $relatedAttribute => array_values(array_diff($prevIds, $nextIds)),
                ])->execute();
            }

            $orderIndex = static::getOrderFields()[$relationName] ?? null;

            // Insert
            $this->insertRelationsByIds($table, $ownAttribute, $relatedAttribute, $nextIds, $prevIds, $orderIndex);
        }
    }

    protected function insertRelationsByIds($table, $ownAttribute, $relatedAttribute, $nextIds, $prevIds, $orderIndex = null)
    {
        $this->insertRelationsByIdsInternal($table, $ownAttribute, $relatedAttribute, $nextIds, $prevIds, $orderIndex);
    }

    protected function saveRelationDataBeforeSelf()
    {
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if ($isIds) {
                continue;
            }

            // Get relation and check it
            /** @var ActiveQuery $relation */
            $relation = $this->getRelation($relationName);

            if (!$relation->multiple) {
                // has one
                $selfAttribute = array_values($relation->link)[0];
                $relationAttribute = array_keys($relation->link)[0];

                // data no set
                if (!$this->isRelationPopulated($relationName)) {
                    continue;
                }

                // Check link id attribute is located in self model
                if ($this::primaryKey()[0] === $relationAttribute) {
                    /** @var ActiveRecord $value */
                    $value = $this->$relationName;
                    if ($value === null) {
                        // delete
                        $related = $relation->one();
                        if ($related) {
                            $related->delete();
                        }
                    } else {
                        // update or insert
                        $value->save();
                        $this->$selfAttribute = $value->$relationAttribute;
                    }
                }
            }
        }
    }

    protected function saveRelationDataAfterSelf()
    {
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if ($isIds) {
                continue;
            }

            // Get relation and check it
            /** @var ActiveQuery $relation */
            $relation = $this->getRelation($relationName);

            if (!$relation->multiple) {
                // has one
                $selfAttribute = array_values($relation->link)[0];
                $relationAttribute = array_keys($relation->link)[0];

                // data no set
                if (!$this->isRelationPopulated($relationName)) {
                    continue;
                }

                // Check link id attribute is located in relation model
                if ($this::primaryKey()[0] === $selfAttribute) {
                    /** @var ActiveRecord $value */
                    $value = $this->$relationName;
                    if ($value === null) {
                        // delete
                        $related = $relation->one();
                        if ($related) {
                            $related->delete();
                        }
                    } else {
                        // update or insert
                        $value->$relationAttribute = $this->$selfAttribute;
                        $value->save(false);
                    }
                }
            } elseif (!$relation->via) {
                // has many
                /* @type ActiveRecord $relatedModel */
                $relatedModel = $relation->modelClass;
                $selfAttribute = array_values($relation->link)[0];
                $relationAttribute = array_keys($relation->link)[0];
                $primaryKey = $relatedModel::primaryKey()[0];

                // update or insert
                foreach ($this->$relationName as $item) {
                    /** @type Model $item */
                    $item->$relationAttribute = $this->$selfAttribute;
                    $item->save(false);
                }

                // delete
                $itemsToDelete = $relation
                    ->where([
                        'not',
                        [
                            $primaryKey => ArrayHelper::getColumn($this->$relationName, $primaryKey),
                        ],
                    ])
                    ->all();
                foreach ($itemsToDelete as $item) {
                    $item->delete();
                }
            } else {
                // many many
                /* @type ActiveRecord $relatedModel */
                $relatedModel = $relation->modelClass;
                $relatedAttribute = reset($relation->link);
                $primaryKey = $relatedModel::primaryKey()[0];

                if (is_array($relation->via)) {
                    // via
                    /* @var $modelClass \yii\db\ActiveRecord */
                    $modelClass = $relation->via[1]->modelClass;
                    $table = $modelClass::tableName();
                    $ownAttribute = key($relation->via[1]->link);
                } else {
                    // viaTable
                    $table = $relation->via->from[0];
                    $ownAttribute = key($relation->via->link);
                }

                $prevIds = $this->getRelationIds($relationName);

                // delete junction + related model
                $itemsToDelete = $relation
                    ->where([
                        'not',
                        [
                            $primaryKey => array_filter(ArrayHelper::getColumn($this->$relationName, $primaryKey)),
                        ],
                    ])
                    ->all();
                if (count($itemsToDelete) > 0) {
                    // delete related model
                    foreach ($itemsToDelete as $item) {
                        $item->delete();
                    }
                    // delete junction
                    static::getDb()->createCommand()->delete($table, [
                        $ownAttribute => $this->primaryKey,
                        $relatedAttribute => ArrayHelper::getColumn($itemsToDelete, $primaryKey),
                    ])->execute();
                }

                // update or insert
                foreach ($this->$relationName as $item) {
                    /** @type Model $item */
                    $item->save(false);
                }

                $nextIds = ArrayHelper::getColumn($this->$relationName, $primaryKey);

                $this->insertRelationsByIdsInternal($table, $ownAttribute, $relatedAttribute, $nextIds, $prevIds);
            }
        }
    }

    private function insertRelationsByIdsInternal($table, $ownAttribute, $relatedAttribute, $nextIds, $prevIds, $orderIndex = null)
    {
        $attributesNames = [$ownAttribute, $relatedAttribute];
        $newIds = array_values(array_diff($nextIds, $prevIds));

        // Unordered junction
        if (empty($orderIndex)) {
            $attributesValues = array_map(function ($id) {
                return [$this->primaryKey, $id];
            }, $newIds);
        }

        // Add items order if $orderIndex is set
        // Items order is passed via $nextIds array keys so $nextIds values order is important
        else {
            static::getDb()->createCommand()->delete($table, [$ownAttribute => $this->primaryKey])->execute();

            $attributesNames[] = $orderIndex;
            $itemsOrderIndices = array_keys($nextIds);
            $attributesValues = array_map(function ($id, $idOrderIndex) {
                return [$this->primaryKey, $id, $idOrderIndex];
            }, $nextIds, $itemsOrderIndices);
        }

        /** @var Connection $databaseConnection */
        $databaseConnection = static::getDb();
        $databaseConnection
            ->createCommand()
            ->batchInsert($table, $attributesNames, $attributesValues)
            ->execute();
    }
}