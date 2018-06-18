<?php

namespace app\views;

use app\auth\forms\RegistrationForm;
use Yii;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model RegistrationForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'email',
        'code',
    ],
    'submitLabel' => Yii::t('app', 'Подтвердить почту'),
]); ?>

