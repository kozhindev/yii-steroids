<?php

namespace steroids\commands;

use steroids\helpers\DefaultConfig;
use yii\console\controllers\MigrateController;

class MigrateCommand extends MigrateController
{
    public $migrationPath = null;

    public function beforeAction($action)
    {
        $steroidsConfig = [
            'appDir' => STEROIDS_ROOT_DIR . '/app',
            'namespace' => 'app',
        ];

        // Set migration namespaces
        foreach (DefaultConfig::getModuleClasses($steroidsConfig) as $name => $moduleClass) {
            $namespace = preg_replace('/[^\\\\]+$/', 'migrations', $moduleClass);

            // Set alias for load migrations
            if (!preg_match('/^\\\\?app\\\\/', $moduleClass)) {
                $moduleDir = dirname((new \ReflectionClass($moduleClass))->getFileName());

                \Yii::setAlias(
                    '@' . str_replace('\\', '/', $namespace),
                    $moduleDir . '/migrations'
                );
            }

            $this->migrationNamespaces[] = $namespace;
        }

        return parent::beforeAction($action);
    }
}