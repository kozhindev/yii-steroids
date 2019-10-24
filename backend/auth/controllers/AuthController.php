<?php

namespace steroids\auth\controllers;

use Yii;
use yii\web\Controller;
use steroids\auth\forms\RecoveryEmailPasswordConfirmForm;
use steroids\auth\forms\RecoveryEmailPasswordForm;
use steroids\auth\forms\RegistrationConfirmForm;
use steroids\auth\forms\RegistrationForm;
use steroids\auth\forms\LoginForm;

class AuthController extends Controller
{
    /**
     * @var string
     */
    public $loginClass = LoginForm::class;

    /**
     * @var string
     */
    public $registrationClass = RegistrationForm::class;

    /**
     * @var string
     */
    public $registrationConfirmClass = RegistrationConfirmForm::class;

    /**
     * @var string
     */
    public $recoveryClass = RecoveryEmailPasswordForm::class;

    /**
     * @var string
     */
    public $recoveryConfirmClass = RecoveryEmailPasswordConfirmForm::class;

    public static function apiMap()
    {
        return [
            'auth' => [
                'items' => [
                    'registration' => 'POST api/v1/auth/registration',
                    'registration-confirm' => 'POST api/v1/auth/registration/confirm',
                    'login' => 'POST api/v1/auth/login',
                    'recovery' => 'POST api/v1/auth/recovery',
                    'recovery-confirm' => 'POST api/v1/auth/recovery/confirm',
                    'logout' => 'POST api/v1/auth/logout',
                    'ws' => 'GET api/v1/auth/ws',
                ],
            ],
        ];
    }

    /**
     * Registration
     * @return RegistrationForm
     * @throws \Exception
     */
    public function actionRegistration()
    {
        /** @var RegistrationForm $model */
        $model = new $this->registrationClass();
        if ($model->load(Yii::$app->request->post())) {
            $model->register();
        }
        return $model;
    }

    /**
     * Registration
     * @return RegistrationConfirmForm
     * @throws \Exception
     */
    public function actionRegistrationConfirm()
    {
        /** @var RegistrationConfirmForm $model */
        $model = new $this->registrationConfirmClass();
        if ($model->load(Yii::$app->request->post())) {
            $model->confirm();
        }
        return $model;
    }

    /**
     * Login
     * @return LoginForm
     * @throws \Exception
     */
    public function actionLogin()
    {
        /** @var LoginForm $model */
        $model = new $this->loginClass();
        if ($model->load(Yii::$app->request->post())) {
            $model->login();
        }
        return $model;
    }

    /**
     * Recovery request (send email)
     * @return RecoveryEmailPasswordForm
     * @throws \Exception
     */
    public function actionRecovery()
    {
        /** @var RecoveryEmailPasswordForm $model */
        $model = new $this->recoveryClass();
        if ($model->load(Yii::$app->request->post())) {
            $model->send();
        }
        return $model;
    }

    /**
     * Recovery request (confirm and change password)
     * @return RecoveryEmailPasswordConfirmForm
     * @throws \Exception
     */
    public function actionRecoveryConfirm()
    {
        /** @var RecoveryEmailPasswordConfirmForm $model */
        $model = new $this->recoveryConfirmClass();
        if ($model->load(Yii::$app->request->post())) {
            $model->confirm();
        }
        return $model;
    }

    /**
     * Logout
     * @return array
     */
    public function actionLogout()
    {
        return [
            'success' => Yii::$app->user->logout(),
        ];
    }

    public function actionWs()
    {
        return [
            'token' => \Yii::$app->user->refreshWsToken(),
        ];
    }
}
