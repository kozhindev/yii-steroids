<?php

namespace app\views;

use Yii;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<div class="alert alert-warning">
    <?= \Yii::t('steroids', 'Подтвердите ваш e-mail для завершения регистрации') ?>
</div>
<p>
    <?= \Yii::t('steroids', 'Если письмо не пришло, обязательно проверьте папку спам или напишите в техподдержку') ?>
    <?= Html::mailto(Yii::$app->params['adminEmail']) ?>
</p>