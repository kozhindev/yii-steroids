<?php

namespace steroids\modules\gii\models;


/**
 * Trait MigrationPostgresTrait
 * @package steroids\modules\gii\models
 */
trait MigrationPostgresTrait
{
    /**
     * Add single command if it's needed to change 'required' property on Postgres
     *
     * @param MetaItem $oldMetaItem
     * @param MetaItem $newMetaItem
     */
    protected function postgresProcessUpdate($oldMetaItem, $newMetaItem)
    {
        if (!(\Yii::$app->db->getSchema() instanceof \yii\db\pgsql\Schema)) {
            return;
        }

        $this->postgresProcessNotNull($oldMetaItem, $newMetaItem);
        $this->postgresProcessDefaultValue($oldMetaItem, $newMetaItem);
    }

    /**
     * @param MetaItem $oldMetaItem
     * @param MetaItem $newMetaItem
     */
    protected function postgresProcessDefaultValue($oldMetaItem, $newMetaItem)
    {
        // Proceed only if default value has changed
        if ($oldMetaItem->defaultValue == $newMetaItem->defaultValue) {
            return;
        }

        $oldMetaItemClone = clone $oldMetaItem;
        $newMetaItemClone = clone $newMetaItem;

        $oldMetaItemClone->customMigrationColumnType = self::buildDefaultString($oldMetaItem->defaultValue, !!$oldMetaItem->required);
        $newMetaItemClone->customMigrationColumnType = self::buildDefaultString($newMetaItem->defaultValue, !!$newMetaItem->required);
        $this->alterColumn[] = $newMetaItemClone;
        $this->alterColumnDown[] = $oldMetaItemClone;
    }

    /**
     * @param MetaItem $oldMetaItem
     * @param MetaItem $newMetaItem
     */
    protected function postgresProcessNotNull($oldMetaItem, $newMetaItem)
    {
        // If 'required' property wasn't changed, then do not add no additional command
        $oldItemIsRequired = $oldMetaItem->required !== null ?: false;
        $newItemIsRequired = $newMetaItem->required !== null ?: false;

        // Proceed only if 'required' flag is unchecked
        if (!$oldItemIsRequired || $newItemIsRequired) {
            return;
        }

        $oldMetaItemClone = clone $oldMetaItem;
        $newMetaItemClone = clone $newMetaItem;

        $oldMetaItemClone->customMigrationColumnType = '\'SET NOT NULL\'';
        $newMetaItemClone->customMigrationColumnType = '\'DROP NOT NULL\'';
        $this->alterColumn[] = $newMetaItemClone;
        $this->alterColumnDown[] = $oldMetaItemClone;
    }


    /**
     * Copy-pasted from the \yii\db\ColumnSchemaBuilder with minor adjustments for Postgres
     * @see \yii\db\ColumnSchemaBuilder::buildDefaultString()
     *
     * Builds the default value specification for the column.
     * @param mixed $defaultValue
     * @param bool [$isNotNull]
     * @return string string with default value of column.
     */
    protected static function buildDefaultString($defaultValue, $isNotNull = false)
    {
        if ($defaultValue === null) {
            return $isNotNull === false ? '"SET DEFAULT NULL"' : '';
        }

        $string = 'SET DEFAULT ';
        switch (gettype($defaultValue)) {
            case 'integer':
                $string .= (string) $defaultValue;
                break;
            case 'double':
                // ensure type cast always has . as decimal separator in all locales
                $string .= str_replace(',', '.', (string) $defaultValue);
                break;
            case 'boolean':
                $string .= $defaultValue ? 'TRUE' : 'FALSE';
                break;
            case 'object':
                $string .= (string) $defaultValue;
                break;
            default:
                $string .= "'{$defaultValue}'";
        }

        return '"' . $string . '"';
    }
}