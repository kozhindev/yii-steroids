<?php

namespace app\views;

use Yii;
use steroids\modules\user\forms\PasswordResetRequestForm;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model PasswordResetRequestForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'email',
    ],
    'submitLabel' => Yii::t('app', 'Продолжить'),
]); ?>