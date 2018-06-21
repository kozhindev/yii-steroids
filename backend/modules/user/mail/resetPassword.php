<?php

namespace app\views;

use app\core\models\User;
use Yii;
use yii\helpers\Html;
use yii\mail\BaseMessage;
use yii\web\View;

/* @var $this View */
/* @var $message BaseMessage */
/* @var $user User */

$message->setSubject(\Yii::t('app', 'Смена пароля на сайте') . ' ' . Yii::$app->name);
$link = Yii::$app->urlManager->createAbsoluteUrl(['/user/recovery/change', 'token' => $user->emailConfirmKey]);
?>
<h2>
    <?= \Yii::t('app', 'Здравствуйте, {username}.', [
        'username' => Html::encode($user->name ?: $user->email),
    ]) ?>
</h2>
<p><?= \Yii::t('app', 'Для смены пароля перейдите по ссылке:') ?></p>
<p><?= Html::a(Html::encode($link), $link) ?></p>
