<?php

namespace steroids\auth\controllers;

use Yii;
use yii\web\Controller;
use steroids\auth\forms\SocialEmailConfirmForm;
use steroids\auth\forms\SocialEmailForm;
use steroids\auth\forms\SocialLoginForm;

class SocialController extends Controller
{
    public static function apiMap()
    {
        return [
            'auth-social' => [
                'items' => [
                    'login' => 'POST api/v1/auth/social',
                    'email' => 'POST api/v1/auth/social/email',
                    'email-confirm' => 'POST api/v1/auth/social/email/confirm',
                    'proxy' => 'GET api/v1/auth/social/proxy',
                ],
            ],
        ];
    }

    /**
     * Login
     * @return SocialLoginForm
     * @throws \Exception
     */
    public function actionLogin()
    {
        $model = new SocialLoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->login();
        }
        return $model;
    }

    /**
     * Login
     * @return SocialEmailForm
     * @throws \Exception
     */
    public function actionEmail()
    {
        $model = new SocialEmailForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->send();
        }
        return $model;
    }

    /**
     * Login
     * @return SocialEmailConfirmForm
     * @throws \Exception
     */
    public function actionEmailConfirm()
    {
        $model = new SocialEmailConfirmForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->confirm();
        }
        return $model;
    }

    /**
     * Oauth proxy for modal windows
     * @return string
     * @throws \Exception
     */
    public function actionProxy()
    {
        return $this->renderFile(Yii::getAlias('@steroids/auth/views/proxy.php'));
    }
}
