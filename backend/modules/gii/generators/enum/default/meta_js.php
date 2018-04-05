<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\EnumClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $enumClass EnumClass */

$labels = $enumClass->metaClass->renderJsLabels('        ');
$cssClasses = $enumClass->metaClass->renderJsCssClasses('        ');

?>
import Enum from 'yii-steroids/frontend/base/Enum';

import {locale} from 'components';

export default class <?= $enumClass->metaClass->name ?> extends Enum {

<?php foreach ($enumClass->metaClass->meta as $enumMetaItem) { ?>
    static <?= $enumMetaItem->constName ?> = <?= is_numeric($enumMetaItem->value) ? $enumMetaItem->value :  "'" . $enumMetaItem->value . "'" ?>;
<?php } ?>

    static getLabels() {
        return <?= $labels ?>;
    }
<?php if (!empty($cssClasses)) { ?>

    static getCssClasses() {
        return <?= $cssClasses ?>;
    }
<?php } ?>
<?php foreach ($enumClass->metaClass->getCustomColumns() as $columnName) { ?>

    static get<?= ucfirst($columnName) ?>Data()
    {
        return <?= $enumClass->metaClass->renderCustomColumnJs($columnName, '        ') ?>;
    }

    static get<?= ucfirst($columnName) ?>(id)
    {
        const data = this.get<?= ucfirst($columnName) ?>Data();
        return data[id] || null;
    }
<?php } ?>
}
