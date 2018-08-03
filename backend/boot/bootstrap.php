<?php

namespace steroids\boot;

use steroids\helpers\DefaultConfig;
use yii\helpers\ArrayHelper;

require_once __DIR__ . '/../helpers/DefaultConfig.php';

// Load custom config, if exists
$config = DefaultConfig::safeLoadConfig(STEROIDS_ROOT_DIR . '/config.php');

// Init Yii constants
defined('YII_ENV') || define('YII_ENV', isset($config['env']) ? $config['env'] : 'production');
defined('YII_DEBUG') || define('YII_DEBUG', in_array(YII_ENV, ['dev', 'development', 'preview', 'stage']));
defined('YII_ENV_PROD') || define('YII_ENV_PROD', in_array(YII_ENV, ['preview', 'stage', 'beta', 'prod', 'production']));
defined('YII_ENV_DEV') || define('YII_ENV_DEV', in_array(YII_ENV, ['dev', 'development']));
defined('YII_ENV_TEST') || define('YII_ENV_TEST', YII_ENV === 'test');
unset($config['env']);

// Init Yii autoloader
require(STEROIDS_VENDOR_DIR . '/autoload.php');

// Load environment config
$envConfig = DefaultConfig::safeLoadConfig(STEROIDS_APP_DIR . '/config/env/' . YII_ENV . '.php');

// Load main config
$mainConfig = DefaultConfig::safeLoadConfig(STEROIDS_ROOT_DIR . '/app/config/main.php');

// Load web/console config
$appConfig = DefaultConfig::safeLoadConfig(STEROIDS_ROOT_DIR . '/app/config/' . (STEROIDS_IS_CLI && !YII_ENV_TEST ? 'console' : 'web') . '.php');

return ArrayHelper::merge($mainConfig, $appConfig, $envConfig, $config);
