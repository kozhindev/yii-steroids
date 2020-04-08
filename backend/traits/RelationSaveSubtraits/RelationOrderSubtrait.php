<?php


namespace steroids\traits\RelationSaveSubtraits;


use yii\db\ActiveQuery;

trait RelationOrderSubtrait
{
    public static function getOrderFields()
    {
        return [];
    }

    private static function orderIds(&$relationIds, $relationName, $relation, $primaryKey)
    {
        $orderField = static::getOrderFields()[$relationName] ?? null;

        if (!$orderField) {
            return;
        }

        $relatedModelJunctionIndex = $relation->link[$primaryKey];
        /** @var ActiveQuery $viaQuery */
        $viaQuery = $relation->via;
        $junctionRows = $viaQuery
            ->asArray()
            ->indexBy($relatedModelJunctionIndex)
            ->all();

        usort($relationIds, function ($id1, $id2) use ($junctionRows, $orderField) {
            $item1OrderIndex = (int) $junctionRows[$id1][$orderField];
            $item2OrderIndex = (int) $junctionRows[$id2][$orderField];

            if ($item1OrderIndex === $item2OrderIndex) {
                return 0;
            }
            return $item1OrderIndex < $item2OrderIndex ? -1 : 1;
        });
    }
}