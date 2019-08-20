<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;
use yii\helpers\ArrayHelper;

/* @var $modelEntity ModelEntity */

$useClasses = [];
$rules = $modelEntity->renderRules($useClasses);
$behaviors = $modelEntity->renderBehaviors('            ', $useClasses);
$meta = $modelEntity->renderMeta('        ', $useClasses);

if (count($modelEntity->publicRelationItems) > 0) {
    $useClasses[] = 'yii\db\ActiveQuery';
}
foreach ($modelEntity->publicRelationItems as $relationEntity) {
    $useClasses[] = $relationEntity->relationModel;
}

echo "<?php\n";
?>

namespace <?= $modelEntity->getNamespace() ?>\meta;

use steroids\base\Model;
<?php foreach (array_unique($useClasses) as $relationClassName) { ?>
use <?= $relationClassName ?>;
<?php } ?>

/**
<?php foreach ($modelEntity->getPhpDocProperties() as $name => $phpDocType) { ?>
 * @property <?= "{$phpDocType} \${$name}\n" ?>
<?php } ?>
<?php foreach ($modelEntity->publicRelationItems as $relationEntity) { ?>
 * @property-read <?= $relationEntity->relationModelEntry->name ?><?= !$relationEntity->isHasOne ? '[]' : '' ?> <?= "\${$relationEntity->name}\n" ?>
<?php } ?>
 */
abstract class <?= $modelEntity->name ?>Meta extends Model<?= "\n" ?>
{
<?php if (count($modelEntity->getProperties()) > 0) { ?>
<?php foreach ($modelEntity->getProperties() as $key => $value) { ?>
    public $<?= $key ?><?= $value !== null ? ' = ' . $value : '' ?>;
<?php } ?>

<?php } ?>
    public static function tableName()
    {
        return '<?= $modelEntity->tableName ?>';
    }

    public function fields()
    {
        return [
<?php foreach ($modelEntity->publicAttributeItems as $attributeEntity) {
if ($attributeEntity->isPublishToFrontend) {?>
            '<?= $attributeEntity->name ?>',
<?php }
} ?>
        ];<?= "\n" ?>
    }
<?php if (!empty($rules)) { ?>

    public function rules()
    {
        return array_merge(parent::rules(), [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>]);
    }
<?php } ?>
<?php if (!empty($behaviors)) { ?>

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            <?= $behaviors ?>
        ]);
    }
<?php } ?>
<?php foreach ($modelEntity->publicRelationItems as $relationEntity) { ?>

    /**
     * @return ActiveQuery
     */
    public function get<?= ucfirst($relationEntity->name) ?>()
    {
<?php if ($relationEntity->isHasOne || $relationEntity->isHasMany) { ?>
        return $this-><?= $relationEntity->isHasOne ? 'hasOne' : 'hasMany' ?>(<?= $relationEntity->relationModelEntry->name ?>::class, ['<?= $relationEntity->relationKey ?>' => '<?= $relationEntity->selfKey ?>']);
<?php } elseif ($relationEntity->isManyMany) { ?>
        return $this->hasMany(<?= $relationEntity->relationModelEntry->name ?>::class, ['<?= $relationEntity->relationKey ?>' => '<?= $relationEntity->viaRelationKey ?>'])
            ->viaTable('<?= $relationEntity->viaTable ?>', ['<?= $relationEntity->viaSelfKey ?>' => '<?= $relationEntity->selfKey ?>']);
<?php } ?>
    }
<?php } ?>

    public static function meta()
    {
        return array_merge(parent::meta(), <?= $meta ?>);
    }
}
