<?php

namespace steroids\traits;

use yii\base\InvalidArgumentException;
use yii\base\UnknownMethodException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;

trait RelationSimilarTrait
{
    private $_related = [];

    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public function __get($name)
    {
        if (isset($this->_related[$name]) || array_key_exists($name, $this->_related)) {
            return $this->_related[$name];
        }
        $value = parent::__get($name);
        if ($value instanceof ActiveQueryInterface) {
            if (is_subclass_of($value->modelClass, ActiveRecord::class)) {
                return $this->_related[$name] = $value->findFor($name, $this);
            }
            return $value->multiple ? [] : null;
        }

        return $value;
    }

    public function __unset($name)
    {
        if (array_key_exists($name, $this->_related)) {
            unset($this->_related[$name]);
        } elseif ($this->getRelation($name, false) === null) {
            parent::__unset($name);
        }
    }

    public function hasOne($class, $link = null)
    {
        return $this->createRelationQuery($class, $link, false);
    }

    public function hasMany($class, $link = null)
    {
        return $this->createRelationQuery($class, $link, true);
    }

    public function getRelation($name, $throwException = true)
    {
        $getter = 'get' . $name;
        try {
            // the relation could be defined in a behavior
            $relation = $this->$getter();
        } catch (UnknownMethodException $e) {
            if ($throwException) {
                throw new InvalidArgumentException(get_class($this) . ' has no relation named "' . $name . '".', 0, $e);
            }

            return null;
        }
        if (!$relation instanceof ActiveQueryInterface) {
            if ($throwException) {
                throw new InvalidArgumentException(get_class($this) . ' has no relation named "' . $name . '".');
            }

            return null;
        }

        if (method_exists($this, $getter)) {
            // relation name is case sensitive, trying to validate it when the relation is defined within this class
            $method = new \ReflectionMethod($this, $getter);
            $realName = lcfirst(substr($method->getName(), 3));
            if ($realName !== $name) {
                if ($throwException) {
                    throw new InvalidArgumentException('Relation names are case sensitive. ' . get_class($this) . " has a relation named \"$realName\" instead of \"$name\".");
                }

                return null;
            }
        }

        return $relation;
    }

    /**
     * Populates the named relation with the related records.
     * Note that this method does not check if the relation exists or not.
     * @param string $name the relation name, e.g. `orders` for a relation defined via `getOrders()` method (case-sensitive).
     * @param ActiveRecordInterface|array|null $records the related records to be populated into the relation.
     * @see getRelation()
     */
    public function populateRelation($name, $records)
    {
        $this->_related[$name] = $records;
    }

    /**
     * Check whether the named relation has been populated with records.
     * @param string $name the relation name, e.g. `orders` for a relation defined via `getOrders()` method (case-sensitive).
     * @return bool whether relation has been populated with records.
     * @see getRelation()
     */
    public function isRelationPopulated($name)
    {
        return array_key_exists($name, $this->_related);
    }

    /**
     * Returns all populated related records.
     * @return array an array of related records indexed by relation names.
     * @see getRelation()
     */
    public function getRelatedRecords()
    {
        return $this->_related;
    }

    /**
     * @param ActiveRecordInterface|string $class
     * @param array $link
     * @param bool $multiple
     * @return ActiveQuery
     */
    protected function createRelationQuery($class, $link, $multiple)
    {
        $query = new ActiveQuery($class);
        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = $multiple;
        return $query;
    }

}