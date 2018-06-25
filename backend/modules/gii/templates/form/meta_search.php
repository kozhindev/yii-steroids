<?php

namespace app\views;

use steroids\modules\gii\forms\FormEntity;

/* @var $formEntity FormEntity */

$useClasses = [];
if (count($formEntity->publicRelationItems) > 0) {
    $useClasses[] = 'yii\db\ActiveQuery';
}
$rules = $formEntity->renderRules($useClasses);
$behaviors = $formEntity->renderBehaviors('            ', $useClasses);
$meta = $formEntity->renderMeta('        ', $useClasses);

echo "<?php\n";
?>

namespace <?= $formEntity->getNamespace() ?>\meta;

use steroids\base\SearchModel;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>
use <?= $formEntity->queryModelEntity->getClassName() ?>;

abstract class <?= $formEntity->name ?>Meta extends SearchModel
{
<?php foreach ($formEntity->publicAttributeItems as $metaItem) { ?>
    public $<?= $metaItem->name ?>;
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

    public function createQuery()
    {
        return <?= $formEntity->queryModelEntity->name ?>::find();
    }
<?php foreach ($formEntity->publicRelationItems as $relationEntity) { ?>

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
