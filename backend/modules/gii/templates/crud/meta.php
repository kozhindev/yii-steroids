<?php

namespace app\views;

use steroids\modules\gii\generators\crud\CrudGenerator;
use steroids\modules\gii\models\ControllerClass;
use yii\web\View;

/* @var $this View */
/* @var $generator CrudGenerator */
/* @var $controllerClass ControllerClass */

$useClasses = [];
$meta = $controllerClass->metaClass->renderMeta('        ', $useClasses);

echo "<?php\n";
?>

namespace <?= $controllerClass->metaClass->namespace ?>;

use Yii;
use extpoint\yii2\base\CrudController;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>

abstract class <?= $controllerClass->metaClass->name ?> extends CrudController
{
    public static function meta()
    {
        return <?= $meta ?>;
    }
}
