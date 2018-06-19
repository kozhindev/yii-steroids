<?php

namespace steroids\helpers;

use Yii;
use yii\helpers\ArrayHelper;

// Define steroids constants
defined('STEROIDS_IS_CLI') || define('STEROIDS_IS_CLI', php_sapi_name() == 'cli');
defined('STEROIDS_ROOT_DIR') || define(
    'STEROIDS_ROOT_DIR',
    STEROIDS_IS_CLI ? dirname(realpath($_SERVER['argv'][0])) : dirname(dirname($_SERVER['SCRIPT_FILENAME']))
);
defined('STEROIDS_APP_DIR') || define('STEROIDS_APP_DIR', STEROIDS_ROOT_DIR . '/app');
defined('STEROIDS_VENDOR_DIR') || define('STEROIDS_VENDOR_DIR', STEROIDS_ROOT_DIR . '/vendor');

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
     * @param array $yiiCustom
     * @param array $steroidsConfig
     * @return array
     * @return array
     */
    public static function getWebConfig($yiiCustom, $steroidsConfig = [])
    {
        return ArrayHelper::merge(
            [
                'components' => [
                    'request' => [
                        'parsers' => [
                            'application/json' => 'yii\web\JsonParser',
                        ],
                    ],
                ],
            ],
            $yiiCustom
        );
    }

    /**
     * @param array $yiiCustom
     * @param array $steroidsConfig
     * @return array
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getConsoleConfig($yiiCustom, $steroidsConfig = [])
    {
        // Set defaults
        $steroidsConfig = static::getSteroidsConfig($steroidsConfig);

        return ArrayHelper::merge(
            [
                'controllerNamespace' => 'app\commands',
                'controllerMap' => [
                    'migrate' => [
                        'class' => '\steroids\commands\MigrateCommand',
                    ],
                    'steroids' => [
                        'class' => '\steroids\commands\SteroidsCommand',
                    ],
                ],
                'on beforeAction' => function() use ($steroidsConfig) {
                    Yii::setAlias('@tests', STEROIDS_ROOT_DIR . '/tests');
                    Yii::setAlias('@webroot', STEROIDS_ROOT_DIR . '/public');
                }
            ],
            $yiiCustom
        );
    }

    /**
     * @param array $yiiCustom
     * @param array $steroidsConfig
     * @return array
     * @throws \Exception
     */
    public static function getMainConfig($yiiCustom, $steroidsConfig = [])
    {
        // Set defaults
        $steroidsConfig = static::getSteroidsConfig($steroidsConfig);

        $moduleClasses = static::getModuleClasses($steroidsConfig);
        $timeZone = ArrayHelper::getValue($yiiCustom, 'timeZone', 'UTC');

        $config = [
            'basePath' => $steroidsConfig['appDir'],
            'vendorPath' => dirname($steroidsConfig['appDir']) . '/vendor',
            'runtimePath' => dirname($steroidsConfig['appDir']) . '/files/log/runtime',
            'timeZone' => $timeZone,
            'bootstrap' => [
                'log',
                'urlManager',
                'siteMap',
                'frontendState',
            ],
            'components' => [
                'authManager' => [
                    'class' => 'steroids\components\AuthManager',
                ],
                'assetManager' => [
                    'forceCopy' => true,
                    'bundles' => [
                        // Disables Yii & jQuery
                        'yii\web\JqueryAsset' => [
                            'sourcePath' => null,
                            'js' => [],
                        ],
                        'yii\web\YiiAsset' => [
                            'sourcePath' => null,
                            'js' => [],
                        ],
                        'yii\grid\GridViewAsset' => [
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
                    'class' => 'steroids\components\UrlManager',
                ],
                'view' => [
                    'class' => 'steroids\components\View',
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

        return ArrayHelper::merge($config, $yiiCustom);
    }

    /**
     * Recursive scan directory and return all fined module classes
     * @param array|null $steroidsConfig
     * @return array
     * @throws \Exception
     */
    public static function getModuleClasses($steroidsConfig = null)
    {
        $steroidsConfig = static::getSteroidsConfig($steroidsConfig ?: []);

        if (self::$moduleClasses === null) {
            // Require Module class
            $path = __DIR__ . '/../base/Module.php';
            if (file_exists($path)) {
                require_once $path;
            }

            self::$moduleClasses = array_merge(
                static::scanModuleClasses($steroidsConfig['appDir'], $steroidsConfig['namespace']),
                static::scanModuleClasses(dirname(__DIR__) . '/modules', 'steroids\\modules')
            );
        }

        return self::$moduleClasses;
    }

    protected static function getSteroidsConfig($params = [])
    {
        return ArrayHelper::merge(
            [
                'appDir' => STEROIDS_ROOT_DIR . '/app',
                'namespace' => 'app',
            ],
            $params
        );
    }

    /**
     * Scan directory to find modules and submodules
     * @param string $root
     * @param string $namespace
     * @param array $parents
     * @return array
     * @throws \Exception
     */
    protected static function scanModuleClasses($root, $namespace, $parents = [])
    {
        $classes = [];

        foreach (scandir($root) as $dir) {
            // Skip dot folders
            if (!is_dir("$root/$dir") || substr($dir, 0, 1) === '.') {
                continue;
            }

            // Generate class name
            $shortClassName = '';
            foreach ($parents as $parent) {
                $shortClassName .= ucfirst($parent);
            }
            $shortClassName .= ucfirst($dir) . 'Module';
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