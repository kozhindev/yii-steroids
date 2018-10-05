<?php

namespace app\views;

use steroids\modules\gii\forms\EnumEntity;

/* @var $enumEntity EnumEntity */

$labels = $enumEntity->renderJsLabels('        ');
$cssClasses = $enumEntity->renderJsCssClasses('        ');

?>
import Enum from 'yii-steroids/base/Enum';

export default class <?= $enumEntity->name ?>Meta extends Enum {

<?php foreach ($enumEntity->items as $itemEntity) { ?>
    static <?= $itemEntity->getConstName() ?> = <?= $itemEntity->renderConstValue() ?>;
<?php } ?>

    static getLabels() {
        return <?= $labels ?>;
    }
<?php if (!empty($cssClasses)) { ?>

    static getCssClasses() {
        return <?= $cssClasses ?>;
    }
<?php } ?>
<?php foreach ($enumEntity->getCustomColumns() as $columnName) { ?>

    static get<?= ucfirst($columnName) ?>Data()
    {
        return <?= $enumEntity->renderCustomColumnJs($columnName, '        ') ?>;
    }

    static get<?= ucfirst($columnName) ?>(id)
    {
        const data = this.get<?= ucfirst($columnName) ?>Data();
        return data[id] || null;
    }
<?php } ?>
}
