<?php

namespace app\views;

use Yii;
use steroids\modules\user\forms\RegistrationForm;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model RegistrationForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'email',
        'password',
        'passwordAgain',
    ],
    'submitLabel' => Yii::t('app', 'Зарегистрироваться'),
]); ?>
<div class="row">
    <div class="offset-3 col-7">
        <?= \Yii::t('app', 'Регистрируясь, вы принимаете условия {agreement}', [
            'agreement' => Html::a(
                \Yii::t('app', 'пользовательского соглашения'),
                ['/user/registration/agreement'],
                ['target' => '_blank']
            ),
        ]) ?>
    </div>
</div>