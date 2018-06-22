<?php

namespace app\views;

use steroids\modules\user\UserModule;
use Yii;
use steroids\modules\user\forms\LoginForm;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model LoginForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'login',
        'password',
        'rememberMe',
        UserModule::getInstance()->enableCaptcha ? 'reCaptcha' : null
    ],
    'submitLabel' => \Yii::t('steroids','Войти'),
]); ?>
<div class="row">
    <div class="offset-3 col-6">
        <?= Html::a(\Yii::t('steroids', 'Забыли пароль?'), ['/user/recovery/index']) ?>
    </div>
</div>
