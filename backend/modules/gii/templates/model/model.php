<?php

namespace app\views;

use steroids\modules\gii\forms\ModelEntity;

/* @var $modelEntity ModelEntity */

echo "<?php\n";
?>

namespace <?= $modelEntity->getNamespace() ?>;

use <?= $modelEntity->getClassName() ?>Meta;

class <?= $modelEntity->name ?> extends <?= $modelEntity->name . "Meta\n" ?>
{
}
