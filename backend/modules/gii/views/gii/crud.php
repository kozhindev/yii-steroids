<?php

namespace steroids\views;

use steroids\modules\gii\widgets\CrudForm\CrudForm;

/* @var $this \yii\web\View */
/* @var $initialValues array */

?>

<?= CrudForm::widget([
    'initialValues' => $initialValues
]) ?>
