<?php

namespace steroids\base;

use steroids\auth\TwoFactorModelWorkflow;
use steroids\components\AuthManager;
use steroids\components\FrontendState;
use steroids\components\SiteMap;
use steroids\components\Types;

require(STEROIDS_VENDOR_DIR . '/yiisoft/yii2/BaseYii.php');

class BaseYii extends \yii\BaseYii
{
}

spl_autoload_register(['steroids\base\BaseYii', 'autoload'], true, true);
BaseYii::$classMap = require(STEROIDS_VENDOR_DIR . '/yiisoft/yii2/classes.php');
BaseYii::$container = new \yii\di\Container();
