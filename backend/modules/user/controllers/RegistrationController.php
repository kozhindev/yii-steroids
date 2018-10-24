<?php

namespace steroids\modules\user\controllers;

use steroids\modules\user\forms\meta\RegistrationPhoneFormMeta;
use steroids\modules\user\forms\PhoneConfirmForm;
use steroids\modules\user\forms\RegistrationPhoneForm;
use Yii;
use steroids\modules\user\forms\EmailConfirmForm;
use steroids\modules\user\forms\RegistrationEmailForm;
use steroids\modules\user\UserModule;
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
                    'email' => [
                        'label' => \Yii::t('steroids', 'Регистрация по email'),
                        'url' => ['/user/registration/email'],
                        'urlRule' => 'user/registration/email',
                        'items' => [
                            'email-confirm' => [
                                'label' => \Yii::t('steroids', 'Подтверждение email'),
                                'url' => ['/user/registration/email-confirm'],
                                'urlRule' => 'user/registration/email-confirm',
                            ],
                        ],
                    ],
                    'sms' => [
                        'label' => \Yii::t('steroids', 'Регистрация по телефону'),
                        'url' => ['/user/registration/sms'],
                        'urlRule' => 'user/registration/sms',
                        'items' => [
                            'sms-confirm' => [
                                'label' => \Yii::t('steroids', 'Подтверждение телефона'),
                                'url' => ['/user/registration/sms-confirm'],
                                'urlRule' => 'user/registration/sms-confirm',
                            ],
                        ],
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
            'api.user' => [
                'items' => [
                    'registration' => [
                        'label' => \Yii::t('steroids', 'Регистрация'),
                        'url' => ['/user/registration/api-index'],
                        'urlRule' => '/api/<version>/user/registration',
                        'items' => [
                        ],
                    ],
                    'registration-email-confirm' => [
                        'label' => \Yii::t('steroids', 'Подтверждение email'),
                        'url' => ['/user/registration/api-email-confirm'],
                        'urlRule' => '/api/<version>/user/email-confirm',
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
    }

    public function actionEmail()
    {
        $registrationFormClass = UserModule::getInstance()->modelsMap['RegistrationEmailForm'];

        /** @var RegistrationEmailForm $model */
        $model = new $registrationFormClass();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            \Yii::$app->authEnhancer->emailProvider->start($model->user);

            return $this->redirect(UserModule::getInstance()->registrationRedirectUrl ?: ['success']);
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/registration-email'), [
            'model' => $model,
        ]);
    }

    public function actionEmailConfirm()
    {
        $confirmFormClass = UserModule::getInstance()->modelsMap['EmailConfirmForm'];

        /** @var EmailConfirmForm $model */
        $model = new $confirmFormClass();
        $model->provider = \Yii::$app->authEnhancer->emailProvider;
        if ($model->load(Yii::$app->request->get()) && $model->confirm()) {
            $model->provider->end($model->user);

            \Yii::$app->session->setFlash('success', \Yii::t('steroids', 'Email успешно подтверджен!'));
            return $this->goHome();
        }

        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/email-failure'), [
            'model' => $model,
        ]);
    }

    public function actionSms()
    {
        $registrationFormClass = UserModule::getInstance()->modelsMap['RegistrationPhoneForm'];

        /** @var RegistrationPhoneForm $model */
        $model = new $registrationFormClass();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            \Yii::$app->authEnhancer->phoneProvider->start($model->user);

            return $this->redirect(['sms-code']);
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/registration-phone'), [
            'model' => $model,
        ]);
    }

    public function actionSmsCode()
    {
        $registrationFormClass = UserModule::getInstance()->modelsMap['RegistrationPhoneForm'];

        /** @var PhoneConfirmForm $model */
        $model = new $registrationFormClass();
        $model->provider = \Yii::$app->authEnhancer->phoneProvider;
        if ($model->load(Yii::$app->request->post()) && $model->confirm()) {
            \Yii::$app->authEnhancer->phoneProvider->end($model->user);

            \Yii::$app->session->setFlash('success', \Yii::t('steroids', 'Телефон успешно подтверджен!'));
            return $this->goHome();
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/phone-confirm'), [
            'model' => $model,
        ]);
    }

    public function actionSuccess()
    {
        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/success'));
    }

    public function actionAgreement()
    {
        return $this->render(Yii::$app->view->findOverwriteView('@steroids/modules/user/views/registration/agreement'));
    }




    /**
     * @return RegistrationEmailForm
     */
    public function actionApiIndex()
    {
        $model = new RegistrationEmailForm();
        $model->load(Yii::$app->request->post());
        $model->register();
        return $model;
    }

    /**
     * @return EmailConfirmForm
     */
    public function actionApiEmailConfirm()
    {
        $model = new EmailConfirmForm();
        $model->load(Yii::$app->request->post());
        $model->confirm();
        return $model;
    }
}
