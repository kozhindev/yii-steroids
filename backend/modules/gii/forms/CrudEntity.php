<?php

namespace steroids\modules\gii\forms;

use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\meta\CrudEntityMeta;
use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class CrudEntity extends CrudEntityMeta implements IEntity
{
    /**
     * @return static[]
     */
    public static function findAll()
    {
        $items = [];
        foreach (GiiHelper::findClasses(ClassType::CRUD) as $item) {
            $items[] = new static($item);
        }

        ArrayHelper::multisort($items, 'name');
        return $items;
    }

    public function fields()
    {
        return array_merge(
            $this->attributes(),
            [
                'items',
            ]
        );
    }

    public function save()
    {
        if ($this->validate()) {
            // Lazy create module
            ModuleEntity::findOrCreate($this->moduleId);

            // Create/update meta information
            GiiHelper::renderFile('crud/meta', $this->getMetaPath(), [
                'crudEntity' => $this,
            ]);
            \Yii::$app->session->addFlash('success', 'Meta info ' . $this->name . 'Meta updated');

            // Create controller, if not exists
            if (!file_exists($this->getPath())) {
                GiiHelper::renderFile('crud/controller', $this->getPath(), [
                    'crudEntity' => $this,
                ]);
                \Yii::$app->session->addFlash('success', 'Added controller ' . $this->name);
            }

            return true;
        }
        return false;
    }

    public function getPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/controllers/' . $this->name . '.php';
    }

    public function getMetaPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/controllers/meta/' . $this->name . '/Meta.php';
    }

    public function getControllerId() {
        return preg_replace('/-controller$/', '', Inflector::camel2id($this->name));
    }

    public function getRoutePrefix() {
        $modulePrefix = str_replace('.', '/', $this->moduleId);
        return "/$modulePrefix/{$this->getControllerId()}";
    }
}
