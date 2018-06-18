<?php

namespace app\views;

use Yii;
use steroids\modules\user\forms\PasswordResetChangeForm;
use steroids\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model PasswordResetChangeForm */

?>

<h1><?= Html::encode(Yii::$app->siteMap->getTitle()) ?></h1>

<?= ActiveForm::widget([
    'model' => $model,
    'fields' => [
        'newPassword',
        'newPasswordAgain'
    ],
    'submitLabel' => Yii::t('app','Изменить пароль'),
]); ?>
