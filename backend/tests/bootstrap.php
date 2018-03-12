<?php

define('STEROIDS_ROOT_DIR', __DIR__ . '/data/modules');
define('STEROIDS_VENDOR_DIR', __DIR__ . '/../vendor');
define('YII_ENV', 'test');

require(__DIR__ . '/../boot/bootstrap.php');
require(__DIR__ . '/../base/BaseYii.php');

class Yii extends \steroids\base\BaseYii {}

Yii::setAlias('@tests', __DIR__);

//$config = require __DIR__ . '/../web/config/web.php';
//(new yii\web\Application($config));
