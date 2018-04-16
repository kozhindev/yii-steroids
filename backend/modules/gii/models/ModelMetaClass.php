<?php

namespace steroids\modules\gii\models;

use steroids\base\Model;
use steroids\types\StringType;
use yii\db\ActiveQuery;
use yii\db\Schema;

/**
 * @property MetaItem[] $meta
 * @property MetaItem[] $metaWithChild
 * @property Relation[] $relations
 * @property array $phpDocProperties
 * @property array $properties
 * @property string $jsFilePath
 */
class ModelMetaClass extends ModelClass
{
    use MetaClassTrait;

    /**
     * @var ModelClass
     */
    public $modelClass;

    /**
     * @var MetaItem[]
     */
    private $_meta;

    /**
     * @var Relation[]
     */
    private $_relations;

    public function getMeta()
    {
        if ($this->_meta === null) {
            $modelClass = str_replace('\\meta\\', '\\', preg_replace('/Meta$/', '', $this->className));

            if (class_exists($modelClass)) {
                /** @type Model $model */
                $model = new $modelClass();

                $meta = $model::meta();
                if ($meta) {
                    $this->_meta = [];
                    foreach ($meta as $name => $params) {
                        $metaItem = new MetaItem([
                            'name' => $name,
                            'oldName' => $name,
                            'metaClass' => $this,
                        ]);
                        foreach ($params as $key => $value) {
                            $metaItem->$key = $value;
                        }
                        $this->_meta[] = $metaItem;
                    }
                } else {
                    $this->_meta = array_map(function ($attribute) use ($model) {
                        $metaItem = new MetaItem([
                            'name' => $attribute,
                            'oldName' => $attribute,
                            'label' => $model->getAttributeLabel($attribute),
                            'hint' => $model->getAttributeHint($attribute),
                        ]);

                        switch ($attribute) {
                            case 'id':
                                $metaItem->appType = 'primaryKey';
                                break;

                            case 'createTime':
                                $metaItem->appType = 'autoTime';
                                break;

                            case 'updateTime':
                                $metaItem->appType = 'autoTime';
                                $metaItem->touchOnUpdate = true;
                                break;

                            case 'title':
                            case 'name':
                            case 'label':
                                $metaItem->appType = 'string';
                                $metaItem->stringType = StringType::TYPE_TEXT;
                                break;

                            case 'email':
                                $metaItem->appType = 'email';
                                break;

                            case 'phone':
                                $metaItem->appType = 'phone';
                                break;

                            case 'password':
                                $metaItem->appType = 'password';
                                break;
                        }

                        if ($metaItem->appType === 'string') {
                            if (strpos($attribute, 'dateTime') !== false) {
                                $metaItem->appType = 'dateTime';
                            } elseif (strpos($attribute, 'date') !== false) {
                                $metaItem->appType = 'date';
                            } else {
                                $schema = \Yii::$app->db->getTableSchema($model::tableName());
                                $column = $schema ? $schema->getColumn($attribute) : null;
                                $dbType = $column ? $column->dbType : null;

                                switch ($dbType) {
                                    case Schema::TYPE_TEXT:
                                        $metaItem->appType = 'text';
                                        break;

                                    case Schema::TYPE_STRING:
                                        $metaItem->appType = 'string';
                                        break;

                                    case Schema::TYPE_INTEGER:
                                        $metaItem->appType = 'integer';
                                        break;

                                    case Schema::TYPE_DOUBLE:
                                        $metaItem->appType = 'double';
                                        break;

                                    case Schema::TYPE_DATE:
                                        $metaItem->appType = 'date';
                                        break;

                                    case Schema::TYPE_DATETIME:
                                        $metaItem->appType = 'dateTime';
                                        break;

                                    case Schema::TYPE_BOOLEAN:
                                        $metaItem->appType = 'boolean';
                                        break;
                                }
                            }
                        }

                        return $metaItem;
                    }, $model->attributes());
                }
            }
        }
        return $this->_meta;
    }

    public function setMeta($value)
    {
        return $this->_meta = $value;
    }

    public function getRelations()
    {
        if ($this->_relations === null) {
            $this->_relations = [];

            $modelClass = str_replace('\\meta\\', '\\', preg_replace('/Meta$/', '', $this->className));

            if (class_exists($modelClass)) {
                $modelInstance = new $modelClass();

                foreach ((new \ReflectionClass($modelClass))->getMethods() as $methodInfo) {
                    if ($methodInfo->class !== $this->className || strpos($methodInfo->name, 'get') !== 0) {
                        continue;
                    }

                    $activeQuery = $modelInstance->{$methodInfo->name}();
                    if ($activeQuery instanceof ActiveQuery) {
                        if ($activeQuery->multiple && $activeQuery->via) {
                            $this->_relations[] = new Relation([
                                'type' => 'manyMany',
                                'name' => lcfirst(substr($methodInfo->name, 3)),
                                'relationClass' => ModelClass::findOne($activeQuery->modelClass),
                                'relationKey' => array_keys($activeQuery->link)[0],
                                'selfKey' => array_values($activeQuery->via->link)[0],
                                'viaTable' => $activeQuery->via->from[0],
                                'viaRelationKey' => array_values($activeQuery->link)[0],
                                'viaSelfKey' => array_keys($activeQuery->via->link)[0],
                            ]);
                        } else {
                            $this->_relations[] = new Relation([
                                'type' => $activeQuery->multiple ? 'hasMany' : 'hasOne',
                                'name' => lcfirst(substr($methodInfo->name, 3)),
                                'relationClass' => ModelClass::findOne($activeQuery->modelClass),
                                'relationKey' => array_keys($activeQuery->link)[0],
                                'selfKey' => array_values($activeQuery->link)[0],
                            ]);
                        }
                    }
                }
            }
        }
        return $this->_relations;
    }

    public function setRelations($value)
    {
        $this->_relations = $value;
    }

    /**
     * @param string $name
     * @return Relation|null
     */
    public function getRelation($name)
    {
        foreach ($this->relations as $relation) {
            if ($relation->name === $name) {
                return $relation;
            }
        }
        return null;
    }

    public function fields()
    {
        return [
            'className',
            'name',
            'meta',
            'relations',
        ];
    }

}