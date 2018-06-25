<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\helpers\GiiHelper;

/* @var $modelEntity ModelEntity */

$import = [];
$fields = $modelEntity->renderJsFields('        ', $import);
$formatters = $modelEntity->renderJsFormatters('        ', $import);
$baseName = $modelEntity->getOverWriteEntity() ? 'Base' . $modelEntity->name . 'Meta' : 'Model';

?>
<?php if ($modelEntity->getOverWriteEntity()) { ?>
import <?= $baseName ?> from '<?= preg_replace('/\.js$/', '', GiiHelper::getRelativePath($modelEntity->getMetaJsPath(), $modelEntity->getOverWriteEntity()->getMetaJsPath())) ?>';
<?php } else { ?>
import Model from 'yii-steroids/base/Model';
<?php } ?>
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $modelEntity->name ?>Meta extends <?= $baseName ?> {

    static className = '<?= str_replace('\\', '\\\\', $modelEntity->getClassName()) ?>';

    static fields() {
        return <?= preg_replace("/^{\n/", "{\n            ...$baseName.fields(),\n", $fields) ?>;
    }

    static formatters() {
        return <?= preg_replace("/^{\n/", "{\n            ...$baseName.formatters(),\n", $formatters) ?>;
    }

}
