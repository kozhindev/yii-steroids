<?php

namespace app\views;

use Yii;
use steroids\modules\user\forms\RegistrationEmailForm;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model RegistrationEmailForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'email',
        'password',
        'passwordAgain',
        'name'
    ],
    'submitLabel' => Yii::t('steroids', 'Зарегистрироваться'),
]); ?>
<div class="row">
    <div class="offset-3 col-7">
        <?= \Yii::t('steroids', 'Регистрируясь, вы принимаете условия {agreement}', [
            'agreement' => Html::a(
                \Yii::t('steroids', 'пользовательского соглашения'),
                ['/user/registration/agreement'],
                ['target' => '_blank']
            ),
        ]) ?>
    </div>
</div>
