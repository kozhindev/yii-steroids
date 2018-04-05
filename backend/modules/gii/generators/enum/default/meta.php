<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\EnumClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $enumClass EnumClass */

$labels = $enumClass->metaClass->renderLabels('        ');
$cssClasses = $enumClass->metaClass->renderCssClasses('        ');

echo "<?php\n";
?>

namespace <?= $enumClass->metaClass->namespace ?>;

use Yii;
use steroids\base\Enum;

abstract class <?= $enumClass->metaClass->name ?> extends Enum
{
<?php foreach ($enumClass->metaClass->meta as $enumMetaItem) { ?>
    const <?= $enumMetaItem->constName ?> = <?= is_numeric($enumMetaItem->value) ? $enumMetaItem->value :  "'" . $enumMetaItem->value . "'" ?>;
<?php } ?>

    public static function getLabels()
    {
        return <?= $labels ?>;
    }
<?php if (!empty($cssClasses)) { ?>

    public static function getCssClasses()
    {
        return <?= $cssClasses ?>;
    }
<?php } ?>
<?php foreach ($enumClass->metaClass->getCustomColumns() as $columnName) { ?>

    public static function get<?= ucfirst($columnName) ?>Data()
    {
        return <?= $enumClass->metaClass->renderCustomColumn($columnName, '        ') ?>;
    }

    public static function get<?= ucfirst($columnName) ?>($id)
    {
        $data = static::get<?= ucfirst($columnName) ?>Data();
        return isset($data[$id]) ? $data[$id] : null;
    }
<?php } ?>
}
