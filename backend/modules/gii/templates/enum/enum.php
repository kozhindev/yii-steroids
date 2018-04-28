<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\EnumClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $enumClass EnumClass */

echo "<?php\n";
?>

namespace <?= $enumClass->namespace ?>;

use <?= $enumClass->metaClass->className ?>;

class <?= $enumClass->name ?> extends <?= $enumClass->metaClass->name . "\n" ?>
{
}
