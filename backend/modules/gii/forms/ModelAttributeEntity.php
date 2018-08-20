<?php

namespace steroids\modules\gii\forms;

use steroids\base\Model;
use steroids\modules\gii\forms\meta\ModelAttributeEntityMeta;
use steroids\modules\gii\traits\CustomPropertyTrait;
use yii\helpers\ArrayHelper;

/**
 * @property-read bool $isProtected
 */
class ModelAttributeEntity extends ModelAttributeEntityMeta
{
    use CustomPropertyTrait;

    /**
     * @var ModelEntity
     */
    public $modelEntity;

    /**
     * @var string
     */
    public $customMigrationColumnType;

    /**
     * @param ModelEntity $entity
     * @param string $classType
     * @return static[]
     * @throws \ReflectionException
     */
    public static function findAll($entity)
    {
        /** @var Model $className */
        $className = $entity->getClassName();

        /** @var Model $className */
        $items = [];
        foreach ($className::meta() as $attribute => $item) {
            // Legacy support
            if (isset($item['required'])) {
                $item['isRequired'] = true;
                unset($item['required']);
            }

            $items[] = new static(array_merge(
                [
                    'name' => $attribute,
                    'prevName' => $attribute,
                    'appType' => 'string',
                    'modelEntity' => $entity,
                ],
                $item
            ));
        }
        return $items;
    }

    /**
     * @param string $dbType
     * @return array|null
     */
    public static function parseDbType($dbType)
    {
        return preg_match('/^([^(]+)(\(([^)]+)\))?/', $dbType, $matches)
            ? count($matches) > 2 ? [$matches[1], $matches[3]] : [$matches[1]]
            : null;
    }

    public function onUnsafeAttribute($name, $value)
    {
        $this->setCustomProperty($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return array_merge(
            array_diff($this->attributes(), ['modelEntity', 'customMigrationColumnType']),
            array_keys($this->getCustomProperties()),
            ['isProtected']
        );
    }

    /**
     * Formats:
     *  - string
     *  - string NOT NULL
     *  - string(32)
     *  - varchar(255) NOT NULL
     * @return string|null
     */
    public function getDbType()
    {
        return \Yii::$app->types->getType($this->appType)->giiDbType($this);
    }

    /**
     * @return string
     */
    public function getPhpDocType()
    {
        static $typeMap = [
            'bigint' => 'integer',
            'integer' => 'integer',
            'smallint' => 'integer',
            'boolean' => 'boolean',
            'float' => 'double',
            'double' => 'double',
            'binary' => 'resource',
        ];

        $dbType = $this->getDbType();
        $type = $dbType ? (array)self::parseDbType($dbType)[0] : null;
        return ArrayHelper::getValue($typeMap, $type, 'string');
    }

    public function renderMigrationColumnType()
    {
        if ($this->customMigrationColumnType !== null) {
            return $this->customMigrationColumnType;
        }

        $map = [
            'pk' => 'primaryKey',
            'bigpk' => 'bigPrimaryKey',
            'char' => 'char',
            'string' => 'string',
            'text' => 'text',
            'smallint' => 'smallInteger',
            'integer' => 'integer',
            'bigint' => 'bigInteger',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'time' => 'time',
            'date' => 'date',
            'binary' => 'binary',
            'boolean' => 'boolean',
            'money' => 'money',
        ];
        $dbType = $this->getDbType() ?: 'string';
        $parts = self::parseDbType($dbType);

        if (isset($map[$parts[0]])) {
            $arguments = count($parts) > 1 ? implode(', ', array_slice($parts, 1)) : '';

            // 'required' property is handled separately for Postgres
            $isPostgres = \Yii::$app->db->getSchema() instanceof \yii\db\pgsql\Schema;
            $notNull = !$isPostgres && $this->isRequired ? '->notNull()' : '';
            $defaultValue = !$isPostgres && $this->defaultValue !== null && $this->defaultValue !== ''
                ? '->defaultValue(' . (preg_match('/^[0-9]+$/', $this->defaultValue) ? $this->defaultValue : "'" . $this->defaultValue . "'") . ')'
                : '';

            return '$this->' . $map[$parts[0]] . '(' . $arguments . ')' . $notNull . $defaultValue;
        } else {
            return "'$dbType'";
        }
    }

    public function getIsProtected()
    {
        if (!class_exists($this->modelEntity->getClassName())) {
            return false;
        }

        $info = new \ReflectionClass($this->modelEntity->getClassName());
        /** @var Model $parentClassName */
        $parentClassName = $info->getParentClass()->getParentClass()->name;

        if (method_exists($parentClassName, 'meta')) {
            $meta = $parentClassName::meta();
            return ArrayHelper::keyExists($this->name, $meta);
        }

        return false;
    }

}
