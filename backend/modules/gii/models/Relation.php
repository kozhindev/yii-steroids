<?php

namespace steroids\modules\gii\models;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * @property-read boolean $isHasOne
 * @property-read boolean $isHasMany
 * @property-read boolean $isManyMany
 * @property-read string $relationModelClassName
 * @property-read MetaItem $viaRelationMetaItem
 * @property-read MetaItem $viaSelfMetaItem
 */
class Relation extends Object implements Arrayable
{
    use ArrayableTrait;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var ModelClass
     */
    public $relationClass;

    /**
     * @var string
     */
    public $relationKey;

    /**
     * @var string
     */
    public $selfKey;

    /**
     * @var string
     */
    public $viaTable;

    /**
     * @var string
     */
    public $viaRelationKey;

    /**
     * @var string
     */
    public $viaSelfKey;

    public function getIsHasOne()
    {
        return $this->type === 'hasOne';
    }

    public function getIsHasMany()
    {
        return $this->type === 'hasMany';
    }

    public function getIsManyMany()
    {
        return $this->type === 'manyMany';
    }

    /**
     * @return string
     */
    public function getRelationModelClassName()
    {
        return $this->relationClass->className;
    }

    /**
     * @return MetaItem|null
     */
    public function getViaRelationMetaItem()
    {
        return $this->relationClass->metaClass->getMetaItem($this->viaRelationKey);
    }

    /**
     * @return MetaItem|null
     */
    public function getViaSelfMetaItem()
    {
        return $this->relationClass->metaClass->getMetaItem($this->viaSelfKey);
    }

    public function fields()
    {
        $classInfo = new \ReflectionClass($this);
        $fields = [];
        foreach ($classInfo->getProperties() as $property) {
            if ($property->name === 'relationClass') {
                $fields[] = 'relationModelClassName';
            } elseif ($property->isPublic() && $property->class === static::className()) {
                $fields[] = $property->getName();
            }
        }
        return $fields;
    }
}