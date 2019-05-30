<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;

/* @var $modelEntity ModelEntity */

$import = [];
$fields = $modelEntity->renderJsFields('        ', $import);
$formatters = $modelEntity->renderJsFormatters('        ', $import);
$baseName = 'Model';

?>
import Model from 'yii-steroids/base/Model';
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $modelEntity->name ?>Meta extends <?= $baseName ?> {

    static className = '<?= str_replace('\\', '\\\\', $modelEntity->getClassName()) ?>';

    static fields() {
        return <?= preg_replace("/^{\n/", "{\n", $fields) ?>;
    }

    static formatters() {
        return <?= preg_replace("/^{\n/", "{\n", $formatters) ?>;
    }

}
