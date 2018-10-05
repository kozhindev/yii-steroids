<?php

namespace steroids\traits;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait RelationSaveTrait
{
    private $_listenRelations = [];

    /**
     * @param string|array $relationNames
     */
    public function listenRelationIds($relationNames)
    {
        $relationNames = (array)$relationNames;
        foreach ($relationNames as &$path) {
            // Normalize (remove Ids suffix)
            if (is_string($path)) {
                $path = implode('.', array_map(function ($name) {
                    return $this->getRelationNameByIdsAttribute($name);
                }, explode('.', $path)));
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

    protected function loadRelationIds($data, $formName = null)
    {
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if (!$isIds) {
                continue;
            }

            $idsProperty = ArrayHelper::getValue($params, 'idsProperty', $this->getIdsAttributeByRelationName($relationName));
            $this->$idsProperty = ArrayHelper::getValue($data, array_filter([$formName, $idsProperty]));
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

    protected function loadRelationData($data, $formName = null)
    {
        $data = $formName ? ArrayHelper::getValue($data, $formName) : $data;

        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            $listenChildren = ArrayHelper::getValue($params, 'children');
            if ($isIds) {
                $this->loadRelationIds($data, '');
                continue;
            }

            // Get relation and check it
            /** @var ActiveQuery $relation */
            $relation = $this->getRelation($relationName);

            // Check set data
            if (!$data || !ArrayHelper::keyExists($relationName, $data)) {
                continue;
            }

            // Get value data
            $value = ArrayHelper::getValue($data, $relationName);

            if (!$relation->multiple) {
                // has one
                /* @type ActiveRecord $relatedModel */
                $relatedModel = $relation->modelClass;
                $selfAttribute = array_values($relation->link)[0];

                if ($value === null) {
                    // delete
                    $this->$selfAttribute = null;
                    $this->populateRelation($relationName, null);
                } else {
                    // update or insert
                    /* @type Model $related */
                    $related = $relation->one() ?: new $relatedModel();
                    $related->listenRelation($listenChildren);
                    $related->load($value, '');
                    $this->populateRelation($relationName, $related);
                }
            } else {
                // has many, many many
                /* @type Model|FormModel $relatedModel */
                $relatedModel = $relation->modelClass;

                $nextItems = [];
                if (is_array($value)) {
                    if (is_subclass_of($relatedModel, Model::class)) {
                        $primaryKey = $relatedModel::primaryKey()[0];
                        $prevItems = $relation->indexBy($primaryKey)->all();

                        foreach ($value as $valueItem) {
                            // update or insert
                            $pk = ArrayHelper::getValue($valueItem, $primaryKey);

                            /** @var Model $item */
                            $item = ArrayHelper::getValue($prevItems, $pk, new $relatedModel());
                            $item->listenRelation($listenChildren);
                            $item->load($valueItem, '');

                            $nextItems[] = $item;
                        }
                    } elseif (is_subclass_of($relatedModel, FormModel::class)) {
                        foreach ($value as $valueItem) {
                            /** @var Model|FormModel $item */
                            $item = new $relatedModel();
                            $item->listenRelation($listenChildren);
                            $item->load($valueItem, '');

                            $nextItems[] = $item;
                        }
                    }
                }

                $this->populateRelation($relationName, $nextItems);
            }
        }
    }

    protected function validateRelationData()
    {
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if ($isIds) {
                continue;
            }

            // Data no set
            if (!$this->isRelationPopulated($relationName)) {
                continue;
            }

            // Get relation and check it
            /** @var Model|Model[] $related */
            $related = $this->$relationName;

            if (is_array($related)) {
                foreach ($related as $relatedItem) {
                    $relatedItem->validate();
                }
            } elseif ($related) {
                $related->validate();
            }

        }
    }

    protected function getRelationErrors()
    {
        $errors = [];
        foreach ($this->_listenRelations as $relationName => $params) {
            $isIds = ArrayHelper::getValue($params, 'isIds');
            if ($isIds) {
                continue;
            }

            // Get relation
            /** @var ActiveQuery $relation */
            $relation = $this->getRelation($relationName);

            // Check populated
            if (!$this->isRelationPopulated($relationName)) {
                continue;
            }

            // Get errors
            if (!$relation->multiple) {
                /** @type Model $related */
                $related = $this->$relationName;
                if ($related && $related->hasErrors()) {
                    $errors[$relationName] = $related->getErrors();
                }
            } else {
                foreach ($this->$relationName as $related) {
                    /** @type Model $related */
                    if ($related && $related->hasErrors()) {
                        $errors[$relationName][] = $related->getErrors();
                    }
                }
            }
        }
        return $errors;
    }

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
            // Insert
            static::getDb()
                ->createCommand()
                ->batchInsert(
                    $table,
                    [$ownAttribute, $relatedAttribute],
                    array_map(function ($id) {
                        return [$this->primaryKey, $id];
                    }, array_values(array_diff($nextIds, $prevIds)))
                )
                ->execute();
        }
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

                // insert junction
                static::getDb()
                    ->createCommand()
                    ->batchInsert(
                        $table,
                        [$ownAttribute, $relatedAttribute],
                        array_map(function ($id) {
                            return [$this->primaryKey, $id];
                        }, array_values(array_diff($nextIds, $prevIds)))
                    )
                    ->execute();
            }
        }
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
        return $relation->select($primaryKey)->column();
    }

}