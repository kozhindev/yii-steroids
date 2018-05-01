<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\models\MigrationMethods;

/* @var $name string */
/* @var $namespace string */
/* @var $modelEntity ModelEntity */
/* @var $migrationMethods MigrationMethods */

echo "<?php\n";
?>

namespace <?= $namespace ?>;

use steroids\base\Migration;

class <?= $name ?> extends Migration
{
    public function safeUp()
    {
<?php foreach ($migrationMethods->addColumn as $metaItem) { ?>
        $this->addColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->renameColumn as $metaItem) { ?>
        $this->renameColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->prevName ?>', '<?= $metaItem->name ?>');
<?php } ?>
<?php foreach ($migrationMethods->alterColumn as $metaItem) { ?>
        $this->alterColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->dropColumn as $metaItem) { ?>
        $this->dropColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>');
<?php } ?>
<?php if (!empty($migrationMethods->createTable)) { ?>
        $this->createTable('<?= $migrationMethods->nextModelEntity->tableName ?>', [
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
<?php foreach ($migrationMethods->foreignKeys as $relationEntity) { ?>
        $this->createForeignKey('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $relationEntity->selfKey ?>', '<?= $relationEntity->relationModelEntry->tableName ?>', '<?= $relationEntity->relationKey ?>');
<?php } ?>
    }

    public function safeDown()
    {
<?php foreach ($migrationMethods->foreignKeys as $relationEntity) { ?>
        $this->deleteForeignKey('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $relationEntity->selfKey ?>', '<?= $relationEntity->relationModelEntry->tableName ?>', '<?= $relationEntity->relationKey ?>');
<?php } ?>
<?php foreach ($migrationMethods->junctionTables as $junction) { ?>
        $this->dropTable('<?= $junction['table'] ?>');
<?php } ?>
<?php if (!empty($migrationMethods->createTable)) { ?>
        $this->dropTable('<?= $migrationMethods->nextModelEntity->tableName ?>');
<?php } ?>
<?php foreach ($migrationMethods->dropColumn as $metaItem) { ?>
        $this->addColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->renameColumn as $metaItem) { ?>
        $this->renameColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>', '<?= $metaItem->prevName ?>');
<?php } ?>
<?php foreach ($migrationMethods->alterColumnDown as $metaItem) { ?>
        $this->alterColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>', <?= $metaItem->renderMigrationColumnType() ?>);
<?php } ?>
<?php foreach ($migrationMethods->addColumn as $metaItem) { ?>
        $this->dropColumn('<?= $migrationMethods->nextModelEntity->tableName ?>', '<?= $metaItem->name ?>');
<?php } ?>
    }
}
