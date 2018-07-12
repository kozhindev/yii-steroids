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
                'label' => \Yii::t('steroids', 'Восстановление пароля'),
                'url' => ['/user/recovery/index'],
                'urlRule' => 'user/recovery',
                'items' => [
                    'change' => [
                        'label' => \Yii::t('steroids', 'Смена пароля'),
                        'url' => ['/user/recovery/change'],
                        'urlRule' => 'user/recovery/<token>',
                    ],
                ],
            ],
            'api.user' => [
                'items' => [
                    'recovery-send' => [
                        'label' => \Yii::t('steroids', 'Восстановление пароля'),
                        'url' => ['/user/recovery/api-index'],
                        'urlRule' => 'api/user/recovery-send',
                    ],
                    'recovery-change' => [
                        'label' => \Yii::t('steroids', 'Смена пароля'),
                        'url' => ['/user/recovery/api-change'],
                        'urlRule' => 'api/user/recovery-change',
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('info', Yii::t('steroids', 'Вам отправлено письмо с инструкциями по смене пароля'));
            return $this->refresh();
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/recovery/request'), [
            'model' => $model,
        ]);
    }

    public function actionChange($token)
    {
        $model = new PasswordResetChangeForm([
            'token' => $token,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->reset()) {
            Yii::$app->session->setFlash('success', \Yii::t('steroids', 'Новый пароль сохранен. Теперь вы можете войти, используя новый пароль'));
            return $this->redirect(['/user/auth/login']);
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/recovery/reset'), [
            'model' => $model,
        ]);
    }

    /**
     * @return PasswordResetRequestForm
     */
    public function actionApiIndex()
    {
        $model = new PasswordResetRequestForm();
        $model->load(Yii::$app->request->post());
        $model->send();
        return $model;
    }

    /**
     * @param string $token
     * @return PasswordResetChangeForm
     */
    public function actionApiChange($token)
    {
        $model = new PasswordResetChangeForm([
            'token' => $token,
        ]);
        $model->load(Yii::$app->request->post());
        $model->reset();
        return $model;
    }
}
