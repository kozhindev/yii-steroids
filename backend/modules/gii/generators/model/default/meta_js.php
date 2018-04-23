<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\ModelClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $modelClass ModelClass */

$import = [];
$fields = $modelClass->metaClass->renderJsFields('        ', $import);
$formatters = $modelClass->metaClass->renderJsFormatters('        ', $import);

?>
import Model from 'yii-steroids/frontend/base/Model';
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $modelClass->metaClass->name ?> extends Model {

    static className = '<?= str_replace('\\', '\\\\', $modelClass->className) ?>';

    static fields() {
        return <?= $fields ?>;
    }

    static formatters() {
        return <?= $formatters ?>;
    }

}
