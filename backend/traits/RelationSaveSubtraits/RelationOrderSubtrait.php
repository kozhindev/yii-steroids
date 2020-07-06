<?php


namespace steroids\traits\RelationSaveSubtraits;


use yii\db\ActiveQuery;

trait RelationOrderSubtrait
{
    /**
     * Returns order field names for model relations
     *
     * @return array Associative array of the pairs for the fields to order
     * where keys are relation names, and values are junction table order indices
     *
     * E.g. if the model Route has 'images' relation by the junction table 'route_images_junction',
     * then we need to add column, e.g. 'orderIndex', to that table
     *
     * Then override this function so that it return
     * [
     *      'images' => 'orderIndex',
     * ]
     */
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