<?php

namespace steroids\modules\gii\forms;

use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;

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
        $entity->attributes = GiiHelper::parseClassName($className);

        $entity->populateRelation('relationItems', FormRelationEntity::findAll($entity));
        $entity->populateRelation('attributeItems', FormAttributeEntity::findAll($entity));

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

    public function save() {
        if ($this->validate()) {
            GiiHelper::renderFile('forms/meta', $this->getMetaPath(), [
                'formEntity' => $this,
            ]);
            GiiHelper::renderFile('forms/meta_js', $this->getMetaJsPath(), [
                'formEntity' => $this,
            ]);
            \Yii::$app->session->addFlash('success', 'Meta info form ' . $this->name . 'Meta update');

            // Create model, if not exists
            if (!file_exists($this->getPath())) {
                GiiHelper::renderFile('forms/form', $this->getPath(), [
                    'formEntity' => $this,
                ]);
                \Yii::$app->session->addFlash('success', 'Added form ' . $this->name);
            }

            return true;
        }
        return false;
    }

    public function getPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/' . $this->name . '.php';
    }

    public function getMetaPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/meta/' . $this->name . '/Meta.php';
    }

    public function getMetaJsPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/forms/meta/' . $this->name . '/Meta.js';
    }

    public function renderRules(&$useClasses = [])
    {
        return ModelEntity::exportRules($this->attributeItems, $this->relationItems, $useClasses);
    }
}
