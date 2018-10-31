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
        $this->scanSteroidsNamespaces();

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

    /**
     * Append steroid's modules migration namespaces
     */
    protected function scanSteroidsNamespaces()
    {
        $steroidsModulesDirectory = dirname(__DIR__) . '/modules';
        foreach(scandir($steroidsModulesDirectory) as $dirName) {
            if ($dirName === '.' || $dirName === '..') {
                continue;
            }

            $migrationsDirectory = $steroidsModulesDirectory . '/' . $dirName . '/migrations';
            $namespace = 'steroids\\modules\\' . $dirName . '\\migrations';

            if (!is_dir($migrationsDirectory) || in_array($namespace, $this->migrationNamespaces)) {
                continue;
            }

            \Yii::setAlias('@' . $namespace, $migrationsDirectory);
            $this->migrationNamespaces[] = $namespace;
        }
    }
}