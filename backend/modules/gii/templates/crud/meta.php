<?php

namespace app\views;

use steroids\modules\gii\generators\crud\CrudGenerator;
use steroids\modules\gii\models\ControllerClass;
use yii\web\View;

/* @var $crudEntity ControllerClass */

$useClasses = [];
$meta = $crudEntity->metaClass->renderMeta('        ', $useClasses);

echo "<?php\n";
?>

namespace <?= $crudEntity->metaClass->namespace ?>\meta;

use Yii;
use extpoint\yii2\base\CrudController;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>

abstract class <?= $crudEntity->metaClass->name ?> extends CrudController
{
    public static function meta()
    {
        return <?= $meta ?>;
    }
}
