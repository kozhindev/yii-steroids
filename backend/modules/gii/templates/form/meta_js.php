<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\FormModelClass;
use steroids\modules\gii\models\ModelClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $modelClass ModelClass */
/* @var $formModelClass FormModelClass */

$import = [];
$fields = $formModelClass->metaClass->renderJsFields('        ', $import);

?>
import Model from 'yii-steroids/frontend/base/Model';
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $formModelClass->metaClass->name ?> extends Model {

    static className = '<?= str_replace('\\', '\\\\', $formModelClass->className) ?>';

    static fields() {
        return <?= $fields ?>;
    }

}
