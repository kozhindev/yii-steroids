<?php

namespace app\views;

use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\models\MigrationClass;
use steroids\modules\gii\models\MigrationMethods;
use yii\web\View;

/* @var $this View */
/* @var $generator ModelGenerator */
/* @var $migrationClass MigrationClass */
/* @var $migrationMethods MigrationMethods */

echo "<?php\n";
?>

namespace <?= $migrationClass->namespace ?>;

use steroids\base\Migration;

class <?= $migrationClass->name ?> extends Migration
{
    public function safeUp()
    {
<?php foreach ($migrationMethods->addColumn as $metaItem) { ?>
        $this->addColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->renameColumn as $metaItem) { ?>
        $this->renameColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->oldName ?>', '<?= $metaItem->name ?>');
<?php } ?>
<?php foreach ($migrationMethods->alterColumn as $metaItem) { ?>
        $this->alterColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->dropColumn as $metaItem) { ?>
        $this->dropColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>');
<?php } ?>
<?php if (!empty($migrationMethods->createTable)) { ?>
        $this->createTable('<?= $migrationMethods->modelClass->tableName ?>', [
    <?php foreach ($migrationMethods->createTable as $metaItem) { ?>
        '<?= $metaItem->name ?>' => <?= $metaItem->renderMigrationColumnType() ?>,
    <?php } ?>
    ]);
<?php } ?>
<?php foreach ($migrationMethods->junctionTables as $junction) { ?>
        $this->createTable('<?= $junction['table'] ?>', [
<?php foreach ($junction['columns'] as $columnName => $columnType) { ?>
            '<?= $columnName ?>' => <?= $columnType ?>,
<?php } ?>
        ]);
<?php } ?>
<?php foreach ($migrationMethods->foreignKeys as $relation) { ?>
        $this->createForeignKey('<?= $migrationMethods->modelClass->tableName ?>', '<?= $relation->selfKey ?>', '<?= $relation->relationClass->tableName ?>', '<?= $relation->relationKey ?>');
<?php } ?>
    }

    public function safeDown()
    {
<?php foreach ($migrationMethods->foreignKeys as $relation) { ?>
        $this->deleteForeignKey('<?= $migrationMethods->modelClass->tableName ?>', '<?= $relation->selfKey ?>', '<?= $relation->relationClass->tableName ?>', '<?= $relation->relationKey ?>');
<?php } ?>
<?php foreach ($migrationMethods->junctionTables as $junction) { ?>
        $this->dropTable('<?= $junction['table'] ?>');
<?php } ?>
<?php if (!empty($migrationMethods->createTable)) { ?>
        $this->dropTable('<?= $migrationMethods->modelClass->tableName ?>');
<?php } ?>
<?php foreach ($migrationMethods->dropColumn as $metaItem) { ?>
        $this->addColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->renameColumn as $metaItem) { ?>
        $this->renameColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>', '<?= $metaItem->oldName ?>');
<?php } ?>
<?php foreach ($migrationMethods->alterColumnDown as $metaItem) { ?>
        $this->alterColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->addColumn as $metaItem) { ?>
        $this->dropColumn('<?= $migrationMethods->modelClass->tableName ?>', '<?= $metaItem->name ?>');
<?php } ?>
    }
}
