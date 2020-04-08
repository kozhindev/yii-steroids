<?php


namespace steroids\traits\RelationSaveSubtraits;


use steroids\base\FormModel;
use steroids\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait RelationLoadSubtrait
{
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
}