<?php

namespace steroids\modules\user\controllers;

use steroids\modules\user\forms\EmailConfirmForm;
use steroids\modules\user\forms\RegistrationForm;
use steroids\modules\user\UserModule;
use Yii;
use yii\web\Controller;
use steroids\widgets\ActiveForm;

class RegistrationController extends Controller
{
    public static function siteMap()
    {
        return [
            'user.registration' => [
                'label' => \Yii::t('steroids', 'Регистрация'),
                'url' => ['/user/registration/index'],
                'urlRule' => 'user/registration',
                'items' => [
                    'email-confirm' => [
                        'label' => \Yii::t('steroids', 'Подтверждение email'),
                        'url' => ['/user/registration/email-confirm'],
                        'urlRule' => 'user/registration/email-confirm',
                    ],
                    'success' => [
                        'label' => \Yii::t('steroids', 'Вы зарегистрировались'),
                        'url' => ['/user/registration/success'],
                        'urlRule' => 'user/registration/success',
                    ],
                    'agreement' => [
                        'label' => \Yii::t('steroids', 'Пользовательское соглашение'),
                        'url' => ['/user/registration/agreement'],
                        'urlRule' => 'user/registration/agreement',
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new RegistrationForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->redirect(UserModule::getInstance()->registrationRedirectUrl ?: ['success']);
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/registration/registration'), [
            'model' => $model,
        ]);
    }

    public function actionEmailConfirm()
    {
        $model = new EmailConfirmForm();
        $model->load(array_merge(
            Yii::$app->request->get(),
            Yii::$app->request->post()
        ));

        if ($model->confirm()) {
            \Yii::$app->session->setFlash('success', \Yii::t('steroids', 'Email успешно подтверджен!'));
            return $this->goHome();
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/registration/email-confirm'), [
            'model' => $model,
        ]);
    }

    public function actionSuccess()
    {
        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/registration/success'));
    }

    public function actionAgreement()
    {
        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/registration/agreement'));
    }
}