<?php

namespace app\views;

use steroids\modules\gii\forms\EnumEntity;

/* @var $enumEntity EnumEntity */

echo "<?php\n";
?>

namespace <?= $enumEntity->getNamespace() ?>;

use <?= $enumEntity->getNamespace() ?>\meta\<?= $enumEntity->name ?>Meta;

class <?= $enumEntity->name ?> extends <?= $enumEntity->name . "Meta\n" ?>
{
}
