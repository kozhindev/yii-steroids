<?php

namespace steroids\modules\gii\models;
use steroids\modules\gii\forms\ModelAttributeEntity;

/**
 * Trait MigrationPostgresTrait
 * @package steroids\modules\gii\models
 */
trait MigrationPostgresTrait
{
    /**
     * Add single command if it's needed to change 'required' property on Postgres
     * @param ModelAttributeEntity $prev
     * @param ModelAttributeEntity $next
     * @throws \yii\base\NotSupportedException
     */
    protected function postgresProcessUpdate($prev, $next)
    {
        if (!(\Yii::$app->db->getSchema() instanceof \yii\db\pgsql\Schema)) {
            return;
        }

        $this->postgresProcessNotNull($prev, $next);
        $this->postgresProcessDefaultValue($prev, $next);
    }

    /**
     * @param ModelAttributeEntity $prev
     * @param ModelAttributeEntity $next
     */
    protected function postgresProcessDefaultValue($prev, $next)
    {
        // Proceed only if default value has changed
        if ($prev->defaultValue == $next->defaultValue) {
            return;
        }

        $prevAttributeEntityClone = clone $prev;
        $nextAttributeEntityClone = clone $next;

        $prevAttributeEntityClone->customMigrationColumnType = self::buildDefaultString($prev->defaultValue, !!$prev->isRequired);
        $nextAttributeEntityClone->customMigrationColumnType = self::buildDefaultString($next->defaultValue, !!$next->isRequired);
        $this->alterColumn[] = $nextAttributeEntityClone;
        $this->alterColumnDown[] = $prevAttributeEntityClone;
    }

    /**
     * @param ModelAttributeEntity $prev
     * @param ModelAttributeEntity $next
     */
    protected function postgresProcessNotNull($prev, $next)
    {
        // If 'required' property wasn't changed, then do not add no additional command
        $prevItemIsRequired = $prev->isRequired !== null ?: false;
        $nextItemIsRequired = $next->isRequired !== null ?: false;

        // Proceed only if 'required' flag is unchecked
        if (!$prevItemIsRequired || $nextItemIsRequired) {
            return;
        }

        $prevMetaItemClone = clone $prev;
        $nextMetaItemClone = clone $next;

        $prevMetaItemClone->customMigrationColumnType = '\'SET NOT NULL\'';
        $nextMetaItemClone->customMigrationColumnType = '\'DROP NOT NULL\'';
        $this->alterColumn[] = $nextMetaItemClone;
        $this->alterColumnDown[] = $prevMetaItemClone;
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