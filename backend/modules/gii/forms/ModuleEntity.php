<?php

namespace steroids\modules\gii\forms;

use steroids\helpers\DefaultConfig;
use steroids\modules\gii\forms\meta\ModuleEntityMeta;
use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;

class ModuleEntity extends ModuleEntityMeta implements IEntity
{
    /**
     * @return static[]
     */
    public static function findAll()
    {
        $items = [];
        foreach (array_keys(GiiHelper::findModules()) as $id) {
            $items[] = new static(['id' => $id]);
        }

        ArrayHelper::multisort($items, 'name');
        return $items;
    }

    /**
     * @param string $id
     * @return static
     * @throws \Exception
     */
    public static function findOrCreate($id)
    {
        $ids = array_keys(DefaultConfig::getModuleClasses());
        $entity = new static(['id' => $id]);
        if (!in_array($entity->id, $ids)) {
            $entity->save();
        }
        return $entity;
    }

    public function save()
    {
        if ($this->validate()) {
            $ids = [];
            foreach (explode('.', $this->id) as $subId) {
                $ids[] = $subId;
                $name = ucfirst($subId) . 'Module';
                $path = \Yii::getAlias('@app') . '/' . implode('/', $ids) . '/' . $name . '.php';

                if (!file_exists($path)) {
                    GiiHelper::renderFile('module/module', $path, [
                        'moduleEntity' => $this,
                        'name' => $name,
                        'namespace' => 'app\\' . implode('\\', $ids),
                    ]);
                }
                \Yii::$app->session->addFlash('success', 'Added module ' . $name);
            }

            return true;
        }
        return false;
    }

    public function getName()
    {
        $ids = explode('.', $this->id);
        return ucfirst(end($ids)) . 'Module';
    }
}
