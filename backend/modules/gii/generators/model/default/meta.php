<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\ModelClass;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $modelClass ModelClass */

$useClasses = [];
$rules = $modelClass->metaClass->renderRules($useClasses);
$behaviors = $modelClass->metaClass->renderBehaviors('            ', $useClasses);
$meta = $modelClass->metaClass->renderMeta('        ', $useClasses);

if (count($modelClass->metaClass->relations) > 0) {
    $useClasses[] = 'yii\db\ActiveQuery';
}
foreach ($modelClass->metaClass->relations as $relation) {
    $useClasses[] = $relation->relationClass->className;
}

echo "<?php\n";
?>

namespace <?= $modelClass->metaClass->namespace ?>;

use app\core\base\AppModel;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>

/**
<?php foreach ($modelClass->metaClass->phpDocProperties as $name => $phpDocType) { ?>
 * @property <?= "{$phpDocType} \${$name}\n" ?>
<?php } ?>
<?php foreach ($modelClass->metaClass->relations as $relation) { ?>
 * @property-read <?= $relation->relationClass->name ?><?= !$relation->isHasOne ? '[]' : '' ?> <?= "\${$relation->name}\n" ?>
<?php } ?>
 */
abstract class <?= $modelClass->metaClass->name ?> extends AppModel
{
<?php if (count($modelClass->metaClass->properties) > 0) { ?>
<?php foreach ($modelClass->metaClass->properties as $key => $value) { ?>
    public $<?= $key ?><?= $value !== null ? ' = ' . $value : '' ?>;
<?php } ?>

<?php } ?>
    public static function tableName()
    {
        return '<?= $modelClass->tableName ?>';
    }

    public function fields()
    {
        return [
<?php foreach ($modelClass->metaClass->metaWithChild as $metaItem) {
if ($metaItem->publishToFrontend) {?>
            '<?= $metaItem->name ?>',
<?php }
} ?>
        ];
    }
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
<?php foreach ($modelClass->metaClass->relations as $relation) { ?>

    /**
     * @return ActiveQuery
     */
    public function get<?= ucfirst($relation->name) ?>()
    {
<?php if ($relation->isHasOne || $relation->isHasMany) { ?>
        return $this-><?= $relation->type ?>(<?= $relation->relationClass->name ?>::className(), ['<?= $relation->relationKey ?>' => '<?= $relation->selfKey ?>']);
<?php } elseif ($relation->isManyMany) { ?>
        return $this->hasMany(<?= $relation->relationClass->name ?>::className(), ['<?= $relation->relationKey ?>' => '<?= $relation->viaRelationKey ?>'])
            ->viaTable('<?= $relation->viaTable ?>', ['<?= $relation->viaSelfKey ?>' => '<?= $relation->selfKey ?>']);
<?php } ?>
    }
<?php } ?>

    public static function meta()
    {
        return <?= $meta ?>;
    }
}
