<?php

namespace steroids\modules\user\controllers;

use Yii;
use yii\web\Controller;
use steroids\widgets\ActiveForm;
use steroids\modules\user\forms\PasswordResetChangeForm;
use steroids\modules\user\forms\PasswordResetRequestForm;

class RecoveryController extends Controller
{
    public static function siteMap()
    {
        return [
            'user.auth.recovery' => [
                'label' => \Yii::t('app', 'Восстановление пароля'),
                'url' => ['/user/recovery/index'],
                'urlRule' => 'user/recovery',
                'items' => [
                    'change' => [
                        'label' => \Yii::t('app', 'Смена пароля'),
                        'url' => ['/user/recovery/change'],
                        'urlRule' => 'user/recovery/<token>',
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('info', Yii::t('app', 'Вам отправлено письмо с инструкциями по смене пароля'));
            return $this->refresh();
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    public function actionChange($token)
    {
        $model = new PasswordResetChangeForm([
            'token' => $token,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Yii::$app->session->setFlash('success', \Yii::t('app', 'Новый пароль сохранен. Теперь вы можете войти, используя новый пароль'));
            return $this->redirect(['/user/auth/login']);
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}
