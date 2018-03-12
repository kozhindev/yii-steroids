<?php

namespace steroids\base;

use yii\db\Exception;
use yii\db\mysql\Schema;

class Migration extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        if (!$options && $this->db->schema instanceof Schema) {
            $options = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        return parent::createTable($table, $columns, $options);
    }

    /**
     * @param string $tableName
     * @param string $from
     * @param string $to
     * @param array $columns
     */
    public function createJunctionTable($tableName, $from, $to, $columns = [])
    {
        $this->createTable($tableName, array_merge(
            [
                $from => $this->integer()->notNull(),
                $to => $this->integer()->notNull()
            ],
            $columns
        ));
        $this->addPrimaryKey(sprintf('%s_pk', $tableName), $tableName, [$from, $to]);
    }

    /**
     * @param string $table
     * @param string|string[] $columns
     * @param string $refTable
     * @param string|string[] $refColumns
     * @param string $delete
     * @param string $update
     */
    public function createForeignKey($table, $columns, $refTable, $refColumns, $delete = 'RESTRICT', $update = 'RESTRICT')
    {
        $name = $this->getForeignKeyName($table, $columns, $refTable, $refColumns);
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * @param string $table
     * @param string|string[] $columns
     * @param string $refTable
     * @param string|string[] $refColumns
     */
    public function deleteForeignKey($table, $columns, $refTable, $refColumns)
    {
        $name = $this->getForeignKeyName($table, $columns, $refTable, $refColumns);
        parent::dropForeignKey($name, $table);

        // Mysql innodb auto create index on add FK, but command `DROP FK..` do not delete it. Fix: Remove index, if exists
        if ($this->db->schema instanceof Schema) {
            try {
                $this->dropIndex($name, $table);
            } catch (Exception $e) {
            }
        }
    }

    /**
     * @param string $table
     * @param string|string[] $columns
     * @param string $refTable
     * @param string|string[] $refColumns
     * @return string
     */
    public static function getForeignKeyName($table, $columns, $refTable, $refColumns)
    {
        $pattern = '/[^0-9a-z-_]+/i';
        $columns = preg_replace($pattern, '', implode(',', (array)$columns));
        $refColumns = preg_replace($pattern, '', implode(',', (array)$refColumns));
        $table = preg_replace($pattern, '', $table);
        $refTable = preg_replace($pattern, '', $refTable);
        $name = sprintf('%s:%s-%s:%s', $table, $columns, $refTable, $refColumns);

        if (strlen($name) > 64) {
            $name = substr($name, 0, 55) . ':' . substr(md5($name), 0, 8);
        }

        return $name;
    }

}
