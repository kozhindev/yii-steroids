<?php

namespace steroids\modules\gii\forms;

use steroids\base\Model;
use steroids\modules\gii\enums\RelationType;
use steroids\modules\gii\forms\meta\ModelRelationEntityMeta;
use yii\db\ActiveQuery;

/**
 * @property-read boolean $isHasOne
 * @property-read boolean $isHasMany
 * @property-read boolean $isManyMany
 * @property-read ModelEntity $relationModelEntry
 * @property-read ModelAttributeEntity $viaRelationAttributeEntry
 * @property-read ModelAttributeEntity $viaSelfAttributeEntry
 * @property-read bool $isProtected
 */
class ModelRelationEntity extends ModelRelationEntityMeta
{
    /**
     * @var ModelEntity
     */
    public $modelEntity;

    /**
     * @param ModelEntity $entity
     * @return static[]
     * @throws \ReflectionException
     */
    public static function findAll($entity)
    {
        /** @var Model $className */
        $className = $entity->getClassName();

        $modelInstance = new $className();
        $modelInfo = (new \ReflectionClass($className));
        $parentInfo = $modelInfo->getParentClass();

        if ($modelInfo->getShortName() . 'Meta' !== $parentInfo->getShortName()) {
            return [];
        }

        $items = [];
        foreach ($parentInfo->getMethods() as $methodInfo) {
            $methodName = $methodInfo->name;

            // Check exists relation in meta class
            if (strpos($methodName, 'get') !== 0 || $methodInfo->class !== $parentInfo->getName()) {
                continue;
            }

            $activeQuery = $modelInstance->$methodName();
            if ($activeQuery instanceof ActiveQuery) {
                if ($activeQuery->multiple && $activeQuery->via) {
                    $items[] = new static([
                        'type' => RelationType::MANY_MANY,
                        'name' => lcfirst(substr($methodInfo->name, 3)),
                        'relationModel' => $activeQuery->modelClass,
                        'relationKey' => array_keys($activeQuery->link)[0],
                        'selfKey' => array_values($activeQuery->via->link)[0],
                        'viaTable' => $activeQuery->via->from[0],
                        'viaRelationKey' => array_values($activeQuery->link)[0],
                        'viaSelfKey' => array_keys($activeQuery->via->link)[0],
                        'modelEntity' => $entity,
                    ]);
                } elseif ($activeQuery->link) {
                    $items[] = new static([
                        'type' => $activeQuery->multiple ? RelationType::HAS_MANY : RelationType::HAS_ONE,
                        'name' => lcfirst(substr($methodInfo->name, 3)),
                        'relationModel' => $activeQuery->modelClass,
                        'relationKey' => array_keys($activeQuery->link)[0],
                        'selfKey' => array_values($activeQuery->link)[0],
                        'modelEntity' => $entity,
                    ]);
                } else {
                    $items[] = new static([
                        'type' => $activeQuery->multiple ? RelationType::HAS_MANY : RelationType::HAS_ONE,
                        'name' => lcfirst(substr($methodInfo->name, 3)),
                        'relationModel' => $activeQuery->modelClass,
                        'modelEntity' => $entity,
                    ]);
                }
            }
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return array_merge(
            array_diff($this->attributes(), ['modelEntity']),
            ['isProtected']
        );
    }

    public function getIsHasOne()
    {
        return $this->type === RelationType::HAS_ONE;
    }

    public function getIsHasMany()
    {
        return $this->type === RelationType::HAS_MANY;
    }

    public function getIsManyMany()
    {
        return $this->type === RelationType::MANY_MANY;
    }

    /**
     * @return ModelEntity|null
     */
    public function getRelationModelEntry()
    {
        return ModelEntity::findOne($this->relationModel);
    }

    /**
     * @return ModelAttributeEntity|null
     */
    public function getViaRelationAttributeEntry()
    {
        return $this->relationModelEntry->getAttributeEntity($this->viaRelationKey);
    }

    /**
     * @return ModelAttributeEntity|null
     */
    public function getViaSelfAttributeEntry()
    {
        return $this->relationModelEntry->getAttributeEntity($this->viaSelfKey);
    }

    public function getIsProtected()
    {
        /*$info = new \ReflectionClass($this->modelEntity->getClassName());
        $parentClassName = $info->getParentClass()->getParentClass()->name;

        if (method_exists($parentClassName, 'meta')) {
            $meta = $parentClassName::meta();
            return ArrayHelper::keyExists($this->name, $meta);
        }*/
        // TODO

        return false;
    }
}
