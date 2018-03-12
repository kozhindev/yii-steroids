<?php

namespace steroids\boot;

use steroids\helpers\DefaultConfig;

require_once __DIR__ . '/../helpers/DefaultConfig.php';

// Get root cwd
defined('STEROIDS_ROOT_DIR') || define('STEROIDS_ROOT_DIR', getcwd());
defined('STEROIDS_APP_DIR') || define('STEROIDS_APP_DIR', STEROIDS_ROOT_DIR . '/app');
defined('STEROIDS_VENDOR_DIR') || define('STEROIDS_VENDOR_DIR', STEROIDS_ROOT_DIR . '/vendor');
defined('STEROIDS_IS_CLI') || define('STEROIDS_IS_CLI', php_sapi_name() == 'cli');

// Load custom config, if exists
$config = DefaultConfig::safeLoadConfig(STEROIDS_ROOT_DIR . '/config.php');

// Init Yii constants
defined('YII_DEBUG') || define('YII_DEBUG', false);
defined('YII_ENV') || define('YII_ENV', isset($config['env']) ? $config['env'] : 'production');
defined('YII_ENV_PROD') || define('YII_ENV_PROD', in_array(YII_ENV, ['preview', 'stage', 'beta', 'prod', 'production']));
defined('YII_ENV_PROD') || define('YII_ENV_DEV', in_array(YII_ENV, ['dev', 'development']));
defined('YII_ENV_TEST') || define('YII_ENV_TEST', YII_ENV === 'test');
unset($config['env']);

// Init Yii autoloader
require(STEROIDS_VENDOR_DIR . '/autoload.php');

// Load environment config
$envConfig = DefaultConfig::safeLoadConfig(STEROIDS_APP_DIR . '/config/env/' . YII_ENV . '.php');

// Load main config
$mainConfig = DefaultConfig::safeLoadConfig(STEROIDS_ROOT_DIR . '/app/config/' . (STEROIDS_IS_CLI ? 'console' : 'web') . '.php');

return \yii\helpers\ArrayHelper::merge(
    $mainConfig,
    $envConfig,
    $config
);
