<?php

namespace app\views;

use steroids\modules\user\forms\PhoneConfirmForm;
use Yii;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model PhoneConfirmForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->title) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'code',
    ],
    'submitLabel' => Yii::t('steroids', 'Зарегистрироваться'),
]); ?>
