<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage */
/* @var $user \app\core\models\User */

$message->setSubject(\Yii::t('app', 'Добро пожаловать на сайт {siteName}', ['siteName' => \Yii::$app->name]));
$confirmUrl = Url::to(['/user/registration/email-confirm', 'email' => $user->email, 'code' => $user->emailConfirmKey], true);
?>
<h2><?= \Yii::t('app', 'Добро пожаловать!') ?></h2>
<h2>
    <?= \Yii::t('app', 'Вы успешно зарегистрировались на сайте {siteName}.', [
        'siteName' => '<strong>' . Html::a(Yii::$app->name, Url::to('/', true)) . '</strong>',
    ]) ?>
</h2>
<p>
    <?= \Yii::t('app', 'Ваш логин') ?>:
    <?= Html::encode($user->email) ?>
</p>
<p>
    <?= \Yii::t('app', 'Для подтверждения регистрации пройдите по ссылке') ?>:
    <?= Html::a($confirmUrl, $confirmUrl) ?>
</p>
