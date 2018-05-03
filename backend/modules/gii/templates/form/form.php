<?php

namespace app\views;

use steroids\modules\gii\forms\FormEntity;

/* @var $formEntity FormEntity */

echo "<?php\n";
?>

namespace <?= $formEntity->getNamespace() ?>;

<?php if ($formEntity->queryModel) { ?>
use yii\db\ActiveQuery;
<?php } ?>
use <?= $formEntity->getNamespace() ?>\meta\<?= $formEntity->name ?>Meta;

class <?= $formEntity->name ?> extends <?= $formEntity->name . "Meta\n" ?>
{
}
