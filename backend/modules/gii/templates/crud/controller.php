<?php

namespace app\views;

use steroids\modules\gii\generators\crud\CrudGenerator;
use steroids\modules\gii\models\ControllerClass;
use yii\web\View;

/* @var $crudEntity ControllerClass */

echo "<?php\n";
?>

namespace <?= $crudEntity->namespace ?>;

use <?= $crudEntity->metaClass->className ?>;

class <?= $crudEntity->name ?> extends <?= $crudEntity->metaClass->name . "\n" ?>
{
}
