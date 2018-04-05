<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\ModelClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $modelClass ModelClass */

echo "<?php\n";
?>

namespace <?= $modelClass->namespace ?>;

use <?= $modelClass->metaClass->className ?>;

class <?= $modelClass->name ?> extends <?= $modelClass->metaClass->name . "\n" ?>
{
}
