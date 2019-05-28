<?php

namespace steroids\modules\gii\forms;

use steroids\base\SearchModel;
use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\GiiModule;
use steroids\modules\gii\helpers\GiiHelper;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * @property-read ModelEntity $queryModelEntity
 */
class FormEntity extends ModelEntity implements IEntity
{
    /**
     * @return static[]
     * @throws \ReflectionException
     */
    public static function findAll()
    {
        $items = [];
        foreach (GiiHelper::findClasses(ClassType::FORM) as $item) {
            $className = GiiHelper::getClassName(ClassType::FORM, $item['moduleId'], $item['name']);
            $items[] = static::findOne($className);
        }

        ArrayHelper::multisort($items, 'name');
        return $items;
    }

    public static function findOne($className)
    {
        $entity = new static();
        $entity->className = $className;
        $entity->attributes = GiiHelper::parseClassName($className);

        /** @var SearchModel $searchModel */
        $searchModel = new $className();

        if (method_exists($searchModel, 'createQuery')) {
            $query = $searchModel->createQuery();
            if (property_exists(get_class($query), 'modelClass')) {
                $entity->queryModel = $query->modelClass;
            }
        }

        $entity->populateRelation('relationItems', FormRelationEntity::findAll($entity));
        $entity->populateRelation('attributeItems', FormAttributeEntity::findAll($entity));

        if (method_exists($searchModel, 'sortFields')) {
            $sortFields = $searchModel->sortFields();
            foreach ($entity->attributeItems as $item) {
                $item->isSortable = in_array($item->name, $sortFields);
            }
        }

        return $entity;
    }

    public function fields()
    {
        return array_merge(
            $this->attributes(),
            [
                'attributeItems',
                'relationItems',
            ]
        );
    }

    public function save()
    {
        if ($this->validate()) {
            // Lazy create module
            ModuleEntity::findOrCreate($this->moduleId);

            if (GiiHelper::isOverWriteClass($this->getClassName()) && GiiModule::getInstance()->showSteroidsEntries) {
                // TODO Save lib class
            }
            GiiHelper::renderFile($this->queryModel ? 'form/meta_search' : 'form/meta_form', $this->getMetaPath(), [
                'formEntity' => $this,
            ]);
            \Yii::$app->session->addFlash('success', 'Meta info form ' . $this->name . 'Meta update');

            // Create model, if not exists
            if (!file_exists($this->getPath())) {
                GiiHelper::renderFile('form/form', $this->getPath(), [
                    'formEntity' => $this,
                ]);
                \Yii::$app->session->addFlash('success', 'Added form ' . $this->name);
            }

            GiiHelper::renderFile('form/meta_js', $this->getMetaJsPath(), [
                'formEntity' => $this,
            ]);

            return true;
        }
        return false;
    }

    public function getClassName()
    {
        return GiiHelper::getClassName(ClassType::FORM, $this->moduleId, $this->name);
    }

    public function getPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/' . $this->name . '.php';
    }

    public function getMetaPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/meta/' . $this->name . 'Meta.php';
    }

    public function getMetaJsPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/meta/' . $this->name . 'Meta.js';
    }

    public function renderRules(&$useClasses = [])
    {
        return ModelEntity::exportRules($this->publicAttributeItems, $this->publicRelationItems, $useClasses);
    }

    /**
     * @return ModelEntity|null
     */
    public function getQueryModelEntity()
    {
        return $this->queryModel ? ModelEntity::findOne($this->queryModel) : null;
    }

    /**
     * @return ActiveQuery
     */
    public function getAttributeItems()
    {
        return $this->hasMany(FormAttributeEntity::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelationItems()
    {
        return $this->hasMany(FormRelationEntity::class);
    }
}
