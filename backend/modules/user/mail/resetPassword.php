<?php

namespace app\views;

use steroids\modules\user\models\User;
use Yii;
use yii\helpers\Html;
use yii\mail\BaseMessage;
use yii\web\View;

/* @var $this View */
/* @var $message BaseMessage */
/* @var $user User */

$message->setSubject(\Yii::t('steroids', 'Смена пароля на сайте') . ' ' . Yii::$app->name);
$link = Yii::$app->urlManager->createAbsoluteUrl(['/user/recovery/change', 'token' => $user->emailConfirmKey]);
?>
<h2>
    <?= \Yii::t('steroids', 'Здравствуйте, {username}.', [
        'username' => Html::encode($user->name ?: $user->email),
    ]) ?>
</h2>
<p><?= \Yii::t('steroids', 'Для смены пароля перейдите по ссылке:') ?></p>
<p><?= Html::a(Html::encode($link), $link) ?></p>
