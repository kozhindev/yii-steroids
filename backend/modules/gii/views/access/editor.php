<?php

namespace steroids\views;

use steroids\modules\gii\widgets\AccessRulesEditor\AccessRulesEditor;
use yii\bootstrap\Nav;

/* @var $this \yii\web\View */
/* @var $editorConfig array */

?>

<?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => \Yii::$app->siteMap->getMenu('admin.access', 1),
]); ?>
<br />

<?= AccessRulesEditor::widget($editorConfig) ?>
