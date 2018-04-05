<?php

namespace steroids\modules\gii\models;

use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * @property-read ModuleClass $moduleClass
 */
class MigrationMethods extends Object
{
    use MigrationPostgresTrait;

    const MIGRATE_MODE_CREATE = 'create';
    const MIGRATE_MODE_UPDATE = 'update';

    /**
     * @var ModelClass|null
     */
    public $oldModelClass;

    /**
     * @var ModelClass
     */
    public $modelClass;

    /**
     * One of value: create, update, none
     * @var string
     */
    public $migrateMode;

    /**
     * @var MetaItem[]
     */
    public $createTable = [];

    /**
     * @var MetaItem[]
     */
    public $addColumn = [];

    /**
     * @var MetaItem[]
     */
    public $alterColumn = [];

    /**
     * @var MetaItem[]
     */
    public $alterColumnDown = [];

    /**
     * @var MetaItem[]
     */
    public $renameColumn = [];

    /**
     * @var MetaItem[]
     */
    public $dropColumn = [];

    /**
     * @var array
     */
    public $junctionTables = [];

    /**
     * @var Relation[]
     */
    public $foreignKeys = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->oldModelClass || $this->migrateMode === self::MIGRATE_MODE_CREATE) {
            $this->processCreateTable();
        } else if ($this->migrateMode === self::MIGRATE_MODE_UPDATE) {
            $this->processAddColumn();
            $this->processUpdateColumn();
            $this->processDropColumn();
        }

        if ($this->migrateMode === self::MIGRATE_MODE_UPDATE || $this->migrateMode === self::MIGRATE_MODE_CREATE) {
            $this->processJunction();
            $this->processForeignKeys();
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->createTable) && empty($this->addColumn) && empty($this->alterColumn)
            && empty($this->alterColumn) && empty($this->alterColumnDown) && empty($this->renameColumn)
            && empty($this->dropColumn) && empty($this->foreignKeys) && empty($this->junctionTables);
    }

    /**
     * @return string
     */
    public function generateName()
    {
        $parts = [];

        if (!$this->oldModelClass) {
            $parts[] = 'create';
            $parts[] = $this->modelClass->name;
        } else {
            $parts[] = $this->modelClass->name;

            $metaItems = [];
            if (!empty($this->addColumn)) {
                $parts[] = 'add';
                $metaItems = $this->addColumn;
            } else if (!empty($this->alterColumn)) {
                $parts[] = 'upd';
                $metaItems = $this->alterColumn;
            } else if (!empty($this->renameColumn)) {
                $parts[] = 'rename';
                $metaItems = $this->renameColumn;
            } else if (!empty($this->dropColumn)) {
                $parts[] = 'drop';
                $metaItems = $this->dropColumn;
            }
            foreach ($metaItems as $metaItem) {
                $parts[] = $metaItem->name;
            }

            if (!empty($this->junctionTables)) {
                $parts[] = 'junction';
                foreach ($this->junctionTables as $junction) {
                    $parts[] = $junction['name'];
                }
            }

            if (!empty($this->foreignKeys)) {
                $parts[] = 'fk';
                foreach ($this->foreignKeys as $relation) {
                    $parts[] = $relation->name;
                }
            }
        }

        $parts = array_slice($parts, 0, 6);
        $parts = array_map('ucfirst', $parts);

        return 'M' . gmdate('ymdHis') . implode('', $parts);
    }

    protected function processCreateTable()
    {
        foreach ($this->modelClass->metaClass->metaWithChild as $metaItem) {
            if ($metaItem->getDbType()) {
                $this->createTable[] = $metaItem;
            }
        }
    }

    protected function processAddColumn()
    {
        foreach ($this->modelClass->metaClass->metaWithChild as $metaItem) {
            if (!$metaItem->oldName && $metaItem->getDbType()) {
                $this->addColumn[] = $metaItem;
            }
        }
    }

    protected function processUpdateColumn()
    {
        /** @var MetaItem $oldMeta [] */
        $oldMeta = $this->oldModelClass ? ArrayHelper::index($this->oldModelClass->metaClass->metaWithChild, 'oldName') : [];
        foreach ($this->modelClass->metaClass->metaWithChild as $metaItem) {
            if (!isset($oldMeta[$metaItem->oldName]) || !$metaItem->oldName) {
                continue;
            }

            if ($metaItem->oldName !== $metaItem->name) {
                $this->renameColumn[] = $metaItem;
            }

            /** @var MetaItem $oldMetaItem */
            $oldMetaItem = $oldMeta[$metaItem->oldName];
            if ($oldMetaItem->renderMigrationColumnType() !== $metaItem->renderMigrationColumnType()) {
                $this->alterColumn[] = $metaItem;
                $this->alterColumnDown[] = $oldMetaItem;
            }

            $this->postgresProcessUpdate($oldMetaItem, $metaItem);
        }
    }

    protected function processDropColumn()
    {
        if ($this->oldModelClass) {
            $metaNames = ArrayHelper::getColumn($this->modelClass->metaClass->metaWithChild, 'oldName');
            foreach ($this->oldModelClass->metaClass->metaWithChild as $oldMetaItem) {
                if (!in_array($oldMetaItem->oldName, $metaNames)) {
                    $this->dropColumn[] = $oldMetaItem;
                }
            }
        }
    }

    protected function processJunction()
    {
        $oldRelationNames = $this->oldModelClass ? ArrayHelper::getColumn($this->oldModelClass->metaClass->relations, 'name') : [];
        foreach ($this->modelClass->metaClass->relations as $relation) {
            if (!$relation->viaTable) {
                continue;
            }

            if ($this->migrateMode === self::MIGRATE_MODE_CREATE || !in_array($relation->name, $oldRelationNames)) {
                $this->junctionTables[] = [
                    'name' => $relation->name,
                    'table' => $relation->viaTable,
                    'columns' => [
                        $relation->viaRelationKey => $relation->viaRelationMetaItem
                            ? $relation->viaRelationMetaItem->renderMigrationColumnType()
                            : '$this->integer()->notNull()',
                        $relation->viaSelfKey => $relation->viaSelfMetaItem
                            ? $relation->viaSelfMetaItem->renderMigrationColumnType()
                            : '$this->integer()->notNull()',
                    ],
                ];
            }
        }
    }

    protected function processForeignKeys()
    {
        $oldRelationNames = $this->oldModelClass ? ArrayHelper::getColumn($this->oldModelClass->metaClass->relations, 'name') : [];
        foreach ($this->modelClass->metaClass->relations as $relation) {
            if ($relation->selfKey === 'id' || !$relation->isHasOne) {
                continue;
            }

            if ($this->migrateMode === self::MIGRATE_MODE_CREATE || !in_array($relation->name, $oldRelationNames)) {
                $this->foreignKeys[] = $relation;
            }
        }
    }

}