<?php

namespace steroids\base;

use yii\base\BaseObject;

abstract class Enum extends BaseObject
{
    /**
     * @return array
     */
    public static function getLabels()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getKeys()
    {
        return array_keys(static::getLabels());
    }

    /**
     * @param string $id
     * @throws \Exception if label doesn't exist
     * @return mixed
     */
    public static function getLabel($id)
    {
        if (!$id) {
            return '';
        }

        $idLabelMap = static::getLabels();
        if (!isset($idLabelMap[$id])) {
            throw new \Exception('Unknown enum id: `' . $id . '`');
        }

        return $idLabelMap[$id];
    }

    /**
     * @return array
     */
    public static function getCssClasses()
    {
        return [];
    }

    /**
     * @param string $id
     * @param string $prefix
     * @return string
     */
    public static function getCssClass($id, $prefix = '')
    {
        $idLabelMap = static::getCssClasses();
        return isset($idLabelMap[$id]) ? $prefix . $idLabelMap[$id] : '';
    }

    /**
     * @param string|null $default
     * @return string
     */
    public static function toMysqlEnum($default = null)
    {
        $keys = static::getKeys();
        if ($default === true) {
            $default = reset($keys);
        }

        return "enum('" . implode("','", $keys) . "')"
            . ($default ? 'NOT NULL DEFAULT ' . \Yii::$app->db->quoteValue($default) : 'NULL');
    }
}