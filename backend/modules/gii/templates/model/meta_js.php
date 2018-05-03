<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;

/* @var $modelEntity ModelEntity */

$import = [];
$fields = $modelEntity->renderJsFields('        ', $import);
$formatters = $modelEntity->renderJsFormatters('        ', $import);

?>
import Model from 'yii-steroids/frontend/base/Model';
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $modelEntity->name ?>Meta extends Model {

    static className = '<?= str_replace('\\', '\\\\', $modelEntity->getClassName()) ?>';

    static fields() {
        return <?= $fields ?>;
    }

    static formatters() {
        return <?= $formatters ?>;
    }

}
