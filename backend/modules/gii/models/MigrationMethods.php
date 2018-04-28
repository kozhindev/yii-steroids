<?php

namespace steroids\modules\gii\models;

use steroids\modules\gii\enums\MigrateMode;
use steroids\modules\gii\enums\RelationType;
use steroids\modules\gii\forms\ModelAttributeEntity;
use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\forms\ModelRelationEntity;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * @property-read ModuleClass $moduleClass
 */
class MigrationMethods extends BaseObject
{
    use MigrationPostgresTrait;

    /**
     * @var ModelEntity|null
     */
    public $prevModelEntity;

    /**
     * @var ModelEntity
     */
    public $nextModelEntity;

    /**
     * @var string
     * @see MigrateMode
     */
    public $migrateMode;

    /**
     * @var ModelAttributeEntity[]
     */
    public $createTable = [];

    /**
     * @var ModelAttributeEntity[]
     */
    public $addColumn = [];

    /**
     * @var ModelAttributeEntity[]
     */
    public $alterColumn = [];

    /**
     * @var ModelAttributeEntity[]
     */
    public $alterColumnDown = [];

    /**
     * @var ModelAttributeEntity[]
     */
    public $renameColumn = [];

    /**
     * @var ModelAttributeEntity[]
     */
    public $dropColumn = [];

    /**
     * @var array
     */
    public $junctionTables = [];

    /**
     * @var ModelRelationEntity[]
     */
    public $foreignKeys = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->prevModelEntity || $this->migrateMode === MigrateMode::CREATE) {
            $this->processCreateTable();
        } else if ($this->migrateMode === MigrateMode::UPDATE) {
            $this->processAddColumn();
            $this->processUpdateColumn();
            $this->processDropColumn();
        }

        if ($this->migrateMode === MigrateMode::UPDATE || $this->migrateMode === MigrateMode::CREATE) {
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

        if (!$this->prevModelEntity) {
            $parts[] = 'create';
            $parts[] = $this->nextModelEntity->name;
        } else {
            $parts[] = $this->nextModelEntity->name;

            /** @var ModelAttributeEntity[] $attributeEntities */
            $attributeEntities = [];
            if (!empty($this->addColumn)) {
                $parts[] = 'add';
                $attributeEntities = $this->addColumn;
            } else if (!empty($this->alterColumn)) {
                $parts[] = 'upd';
                $attributeEntities = $this->alterColumn;
            } else if (!empty($this->renameColumn)) {
                $parts[] = 'rename';
                $attributeEntities = $this->renameColumn;
            } else if (!empty($this->dropColumn)) {
                $parts[] = 'drop';
                $attributeEntities = $this->dropColumn;
            }
            foreach ($attributeEntities as $attributeEntity) {
                $parts[] = $attributeEntity->name;
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
        foreach ($this->nextModelEntity->attributeItems as $attributeEntity) {
            if ($attributeEntity->getDbType()) {
                $this->createTable[] = $attributeEntity;
            }
        }
    }

    protected function processAddColumn()
    {
        foreach ($this->nextModelEntity->attributeItems as $attributeEntity) {
            if (!$attributeEntity->prevName && $attributeEntity->getDbType()) {
                $this->addColumn[] = $attributeEntity;
            }
        }
    }

    protected function processUpdateColumn()
    {
        $prevMeta = $this->prevModelEntity ? ArrayHelper::index($this->prevModelEntity->attributeItems, 'prevName') : [];
        foreach ($this->nextModelEntity->attributeItems as $attributeEntity) {
            if (!isset($prevMeta[$attributeEntity->prevName]) || !$attributeEntity->prevName) {
                continue;
            }

            if ($attributeEntity->prevName !== $attributeEntity->name) {
                $this->renameColumn[] = $attributeEntity;
            }

            /** @var ModelAttributeEntity $prevAttributeEntity */
            $prevAttributeEntity = $prevMeta[$attributeEntity->prevName];
            if ($prevAttributeEntity->renderMigrationColumnType() !== $attributeEntity->renderMigrationColumnType()) {
                $this->alterColumn[] = $attributeEntity;
                $this->alterColumnDown[] = $prevAttributeEntity;
            }

            $this->postgresProcessUpdate($prevAttributeEntity, $attributeEntity);
        }
    }

    protected function processDropColumn()
    {
        if ($this->prevModelEntity) {
            $metaNames = ArrayHelper::getColumn($this->nextModelEntity->attributeItems, 'prevName');
            foreach ($this->prevModelEntity->attributeItems as $prevMetaItem) {
                if (!in_array($prevMetaItem->prevName, $metaNames)) {
                    $this->dropColumn[] = $prevMetaItem;
                }
            }
        }
    }

    protected function processJunction()
    {
        $prevRelationNames = $this->prevModelEntity ? ArrayHelper::getColumn($this->prevModelEntity->relationItems, 'name') : [];
        foreach ($this->nextModelEntity->relationItems as $relationEntity) {
            if (!$relationEntity->viaTable) {
                continue;
            }

            if ($this->migrateMode === MigrateMode::CREATE || !in_array($relationEntity->name, $prevRelationNames)) {
                $this->junctionTables[] = [
                    'name' => $relationEntity->name,
                    'table' => $relationEntity->viaTable,
                    'columns' => [
                        $relationEntity->viaRelationKey => $relationEntity->viaRelationAttributeEntry
                            ? $relationEntity->viaRelationAttributeEntry->renderMigrationColumnType()
                            : '$this->integer()->notNull()',
                        $relationEntity->viaSelfKey => $relationEntity->viaSelfAttributeEntry
                            ? $relationEntity->viaSelfAttributeEntry->renderMigrationColumnType()
                            : '$this->integer()->notNull()',
                    ],
                ];
            }
        }
    }

    protected function processForeignKeys()
    {
        $prevRelationNames = $this->prevModelEntity ? ArrayHelper::getColumn($this->prevModelEntity->relationItems, 'name') : [];
        foreach ($this->nextModelEntity->relationItems as $relationEntity) {
            if ($relationEntity->selfKey === 'id' || !$relationEntity->type === RelationType::HAS_ONE) {
                continue;
            }

            if ($this->migrateMode === MigrateMode::CREATE || !in_array($relationEntity->name, $prevRelationNames)) {
                $this->foreignKeys[] = $relationEntity;
            }
        }
    }

}