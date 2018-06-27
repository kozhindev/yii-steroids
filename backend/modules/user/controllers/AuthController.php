<?php

namespace steroids\modules\user\controllers;

use steroids\modules\user\forms\LoginForm;
use steroids\modules\user\UserModule;
use steroids\widgets\ActiveForm;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{
    const EVENT_BEFORE_LOGIN = 'before_login';
    const EVENT_AFTER_LOGIN = 'after_login';
    const EVENT_BEFORE_LOGOUT = 'before_logout';
    const EVENT_AFTER_LOGOUT = 'after_logout';

    public static function siteMap()
    {
        return [
            'user.auth' => [
                'items' => [
                    'login' => [
                        'label' => \Yii::t('steroids', 'Вход'),
                        'url' => ['/user/auth/login'],
                        'urlRule' => 'user/login',
                    ],
                    'logout' => [
                        'label' => \Yii::t('steroids', 'Выход'),
                        'url' => ['/user/auth/logout'],
                        'urlRule' => 'user/logout',
                    ],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        $redirectUrl = UserModule::getInstance()->loginRedirectUrl ?: Yii::$app->getHomeUrl();
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($redirectUrl);
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post())) {
            $this->trigger(self::EVENT_BEFORE_LOGIN);

            if (!$model->login()) {
                return ActiveForm::renderAjax($model);
            }
            $this->trigger(self::EVENT_AFTER_LOGIN);
            return $this->redirect($redirectUrl);
        }

        return $this->render($this->view->findOverwriteView('@steroids/modules/user/views/auth/login'), [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        $this->trigger(self::EVENT_BEFORE_LOGOUT);
        Yii::$app->user->logout();
        $this->trigger(self::EVENT_AFTER_LOGOUT);
        return $this->goHome();
    }

}
