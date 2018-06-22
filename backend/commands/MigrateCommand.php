<?php

namespace steroids\commands;

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

            \Yii::setAlias('@' . $namespace,$appPath . '/' . $dirName . '/migrations');
        }

        return parent::beforeAction($action);
    }
}