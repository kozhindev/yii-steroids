<?php


namespace steroids\traits\RelationSaveSubtraits;


use steroids\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

trait RelationValidateSubtrait
{
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
}