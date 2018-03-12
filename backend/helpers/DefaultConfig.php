<?php

namespace steroids\helpers;

use yii\helpers\ArrayHelper;

class DefaultConfig
{
    /**
     * @var array
     */
    private static $moduleClasses;

    /**
     * @param string $path
     * @return array
     */
    public static function safeLoadConfig($path)
    {
        $config = [];
        if (file_exists($path)) {
            ob_start();
            $config = require $path;
            ob_end_clean();
        }
        return $config;
    }

    /**
     * @param array|null $custom
     * @param string|null $appDir
     * @param string|null $namespace
     * @return array
     */
    public static function getWebConfig($custom = [], $appDir = null, $namespace = null)
    {
        return ArrayHelper::merge(
            static::getMainConfig([], $appDir, $namespace),
            [
                'components' => [
                    'request' => [
                        'parsers' => [
                            'application/json' => 'yii\web\JsonParser',
                        ],
                    ],
                ],
            ],
            $custom
        );
    }

    /**
     * @param array|null $custom
     * @param string|null $appDir
     * @param string|null $namespace
     * @return array
     */
    public static function getConsoleConfig($custom = [], $appDir = null, $namespace = null)
    {
        $namespaces = [];
        foreach (self::getModuleClasses($appDir, $namespace) as $name => $moduleClass) {
            $namespace = preg_replace('/[^\\\\]+$/', 'migrations', $moduleClass);

            // Set alias for load migrations
            if (!preg_match('/^\\\\?app\\\\/', $moduleClass)) {
                $moduleDir = dirname((new \ReflectionClass($moduleClass))->getFileName());

                \Yii::setAlias(
                    '@' . str_replace('\\', '/', $namespace),
                    $moduleDir . '/migrations'
                );
            }

            $namespaces[] = $namespace;
        }

        return ArrayHelper::merge(
            static::getMainConfig([], $appDir, $namespace),
            [
                'controllerNamespace' => 'app\commands',
                'controllerMap' => [
                    'migrate' => [
                        'class' => '\yii\console\controllers\MigrateController',
                        'migrationPath' => null,
                        'migrationNamespaces' => $namespaces,
                    ],
                ],
            ],
            $custom
        );
    }

    public static function getMainConfig($custom = [], $appDir = null, $namespace = null)
    {
        $appDir = $appDir ?? dirname(dirname(__DIR__)) . '/app';
        $namespace = $namespace ?? 'app';

        $moduleClasses = static::getModuleClasses($appDir, $namespace);
        $timeZone = ArrayHelper::getValue($custom, 'timeZone', 'UTC');

        $config = [
            'basePath' => $appDir,
            'vendorPath' => dirname($appDir) . '/vendor',
            'runtimePath' => dirname($appDir) . '/files/log/runtime',
            'timeZone' => $timeZone,
            'bootstrap' => [
                'log',
                'frontendState',
            ],
            'components' => [
                'authManager' => [
                    'class' => 'steroids\components\AuthManager',
                ],
                'assetManager' => [
                    'forceCopy' => true,
                    'bundles' => [
                        // Disables Yii jQuery
                        'yii\web\JqueryAsset' => [
                            'sourcePath' => null,
                            'js' => [],
                        ],
                        'yii\bootstrap\BootstrapAsset' => [
                            'sourcePath' => null,
                            'css' => [],
                        ],
                        'yii\bootstrap\BootstrapPluginAsset' => [
                            'sourcePath' => null,
                            'js' => [],
                            'css' => [],
                        ],
                    ],
                ],
                'cache' => [
                    'class' => 'yii\caching\FileCache',
                ],
                'db' => [
                    'class' => 'yii\db\Connection',
                    'charset' => 'utf8',
                    'on afterOpen' => function ($event) {
                        $event->sender->createCommand("SET time_zone='" . date('P') . "'")->execute();
                    },
                ],
                'formatter' => [
                    'defaultTimeZone' => $timeZone,
                ],
                'frontendState' => [
                    'class' => 'steroids\components\FrontendState',
                ],
                'log' => [
                    'traceLevel' => YII_DEBUG ? 3 : 0,
                    'targets' => [
                        [
                            'class' => 'yii\log\FileTarget',
                            'levels' => ['error', 'warning'],
                        ],
                    ],
                ],
                'mailer' => [
                    'class' => 'yii\swiftmailer\Mailer',
                ],
                'types' => [
                    'class' => 'steroids\components\Types',
                ],
                'siteMap' => [
                    'class' => 'steroids\components\SiteMap',
                ],
                'urlManager' => [
                    'showScriptName' => false,
                    'enablePrettyUrl' => true,
                    'normalizer' => [
                        'class' => 'yii\web\UrlNormalizer',
                        'collapseSlashes' => true,
                        'normalizeTrailingSlash' => true,
                    ],
                ],
            ],
        ];

        // Add debug module for development env
        if (YII_ENV_DEV) {
            $config['bootstrap'][] = 'debug';
            $config['modules']['debug'] = 'yii\debug\Module';
        }

        // Assets dir for servers
        if (!YII_ENV_DEV) {
            ArrayHelper::setValue($config, 'components.assetManager.basePath', STEROIDS_ROOT_DIR . '/files/assets');
        }

        // Append bootstrap modules
        foreach ($moduleClasses as $name => $moduleClass) {
            // Skip sub modules
            if (strpos($name, '.') !== false) {
                continue;
            }

            // Skip no application modules
            if (!is_subclass_of($moduleClass, '\yii\base\BootstrapInterface')) {
                continue;
            }

            $config['bootstrap'][] = $name;
        }

        // Append modules
        foreach ($moduleClasses as $name => $moduleClass) {
            $path = 'modules.' . str_replace('.', '.modules.', $name);
            ArrayHelper::setValue($config, $path, [
                'class' => $moduleClass,
            ]);
        }

        return $config;
    }


    /**
     * Recursive scan directory and return all fined module classes
     * @param string|null $appDir
     * @param string|null $namespace
     * @return array
     */
    public static function getModuleClasses($appDir = null, $namespace = null)
    {
        // Normalize namespace
        $namespace = trim($namespace, '\\');

        if (self::$moduleClasses === null) {
            // Require Module class
            $path = __DIR__ . '/../base/Module.php';
            if (file_exists($path)) {
                require_once $path;
            }

            self::$moduleClasses = static::scanModuleClasses($appDir, $namespace);
        }

        return self::$moduleClasses;
    }

    /**
     * Scan directory to find modules and submodules
     * @param string $root
     * @param string $namespace
     * @param array $parents
     * @return array
     */
    protected static function scanModuleClasses($root, $namespace, $parents = [])
    {
        $classes = [];

        foreach (scandir($root) as $dir) {
            // Skip dot folders
            if (substr($dir, 0, 1) === '.') {
                continue;
            }

            $shortClassName = ucfirst($dir) . 'Module';
            $classPath = "$root/$dir/$shortClassName.php";

            // Check module class exists
            if (!file_exists($classPath)) {
                continue;
            }

            $className = "$namespace\\$dir\\$shortClassName";
            $moduleId = implode('.', array_merge($parents, [$dir]));

            // Load class
            self::loadClass($classPath, $className);
            $classes[$moduleId] = $className;

            // Scan children
            $classes = array_merge(
                $classes,
                static::scanModuleClasses(
                    "$root/$dir",
                    "$namespace\\$dir",
                    array_merge($parents, [$dir])
                )
            );
        }

        return $classes;
    }

    /**
     * Load module class
     * @params string $path
     * @param string $name
     * @throws \Exception
     */
    protected static function loadClass($path, $name)
    {
        if (!file_exists($path)) {
            throw new \Exception('Not found module class file: ' . $path);
        }
        require_once $path;

        if (!class_exists($name)) {
            throw new \Exception('Not found module class: ' . $name);
        }
        if (!is_subclass_of($name, 'steroids\base\Module')) {
            throw new \Exception('Module class `' . $name . '` is not extends from `steroids\base\Module`');
        }
    }

}