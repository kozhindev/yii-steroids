<?php

namespace steroids\commands;

use yii\base\Module;
use yii\console\controllers\MigrateController;

class MigrateCommand extends MigrateController
{
    public $migrationPath = null;

    public function beforeAction($action)
    {
        $appPath = \Yii::getAlias('@app');

        // Set migration namespaces
        foreach (scandir($appPath) as $dirName) {
            $namespace = 'app\\' . $dirName . '\migrations';

            $this->migrationNamespaces[] = $namespace;
            \Yii::setAlias('@' . $namespace, $appPath . '/' . $dirName . '/migrations');
        }

        $this->scanNamespacesFromModules(\Yii::$app);

        return parent::beforeAction($action);
    }

    protected function scanNamespacesFromModules($module)
    {
        foreach ($module->modules as $module) {
            $info = new \ReflectionClass(is_object($module) ? get_class($module) : $module['class']);
            $namespace = $info->getNamespaceName() . '\\migrations';

            $this->migrationNamespaces[] = $namespace;
            \Yii::setAlias('@' . $namespace, dirname($info->getFileName()) . '/migrations');

            if ($module instanceof Module) {
                $this->scanNamespacesFromModules($module);
            }
        }
    }
}