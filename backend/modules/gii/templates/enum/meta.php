<?php

namespace app\views;

use steroids\modules\gii\forms\EnumEntity;

/* @var $enumEntity EnumEntity */

$labels = $enumEntity->renderLabels('        ');
$cssClasses = $enumEntity->renderCssClasses('        ');

echo "<?php\n";
?>

namespace <?= $enumEntity->getNamespace() ?>\meta;

use Yii;
use steroids\base\Enum;

abstract class <?= $enumEntity->name ?>Meta extends Enum
{
<?php foreach ($enumEntity->items as $itemEntity) { ?>
    const <?= $itemEntity->getConstName() ?> = <?= $itemEntity->renderConstValue() ?>;
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
<?php foreach ($enumEntity->getCustomColumns() as $columnName) { ?>

    public static function get<?= ucfirst($columnName) ?>Data()
    {
        return <?= $enumEntity->renderCustomColumn($columnName, '        ') ?>;
    }

    public static function get<?= ucfirst($columnName) ?>($id)
    {
        return static::getDataValue('<?= $columnName ?>', $id);
    }
<?php } ?>
}
