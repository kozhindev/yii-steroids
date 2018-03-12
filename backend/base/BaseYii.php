<?php

namespace steroids\base;

use steroids\components\AuthManager;
use steroids\components\FrontendState;
use steroids\components\SiteMap;
use steroids\components\Types;

require(STEROIDS_VENDOR_DIR . '/yiisoft/yii2/BaseYii.php');

/**
 * @property-read AuthManager $authManager
 * @property-read FrontendState $frontendState
 * @property-read SiteMap $siteMap
 * @property-read Types $types
 */
class BaseYii extends \yii\BaseYii
{
}

spl_autoload_register(['steroids\base\BaseYii', 'autoload'], true, true);
BaseYii::$classMap = require(STEROIDS_VENDOR_DIR . '/yiisoft/yii2/classes.php');
BaseYii::$container = new \yii\di\Container();
