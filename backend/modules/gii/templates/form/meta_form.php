<?php

namespace app\views;

use steroids\modules\gii\forms\FormEntity;

/* @var $formEntity FormEntity */

$useClasses = [];
if (count($formEntity->relationItems) > 0) {
    $useClasses[] = 'yii\db\ActiveQuery';
}
$rules = $formEntity->renderRules($useClasses);
$behaviors = $formEntity->renderBehaviors('            ', $useClasses);
$meta = $formEntity->renderMeta('        ', $useClasses);

echo "<?php\n";
?>

namespace <?= $formEntity->getNamespace() ?>\meta;

use steroids\base\FormModel;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>

abstract class <?= $formEntity->name ?>Meta extends FormModel
{
<?php foreach ($formEntity->attributeItems as $attributeEntity) { ?>
    public $<?= $attributeEntity->name ?>;
<?php } ?>

<?php if (count($formEntity->getProperties()) > 0) { ?>
<?php foreach ($formEntity->getProperties() as $key => $value) { ?>
    public $<?= $key ?><?= $value !== null ? ' = ' . $value : '' ?>;
<?php } ?>

<?php } ?>
<?php if (!empty($rules)) { ?>
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }
<?php } ?>
<?php if (!empty($behaviors)) { ?>

    public function behaviors()
    {
        return [
            <?= $behaviors ?>
        ];
    }
<?php } ?>
<?php foreach ($formEntity->relationItems as $relationEntity) { ?>

    /**
    * @return ActiveQuery
    */
    public function get<?= ucfirst($relationEntity->name) ?>()
    {
        return $this-><?= $relationEntity->type ?>(<?= $relationEntity->relationModelEntry->name ?>::class);
    }
<?php } ?>

    public static function meta()
    {
        return <?= $meta ?>;
    }
}
