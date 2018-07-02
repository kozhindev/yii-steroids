<?php

namespace steroids\modules\user\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class LoginFormMeta extends FormModel
{
    public $login;
    public $password;
    public $rememberMe;
    public $reCaptcha;
    public $google2faEnable;
    public $google2faCode;

    public function rules()
    {
        return [
            [['login', 'reCaptcha'], 'string', 'max' => 255],
            [['login', 'password'], 'required'],
            ['password', 'string', 'min' => 1, 'max' => 255],
            [['rememberMe','google2faEnable'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'login' => [
                'label' => Yii::t('steroids', 'Логин или email'),
                'isRequired' => true
            ],
            'password' => [
                'label' => Yii::t('steroids', 'Пароль'),
                'appType' => 'password',
                'isRequired' => true
            ],
            'rememberMe' => [
                'label' => Yii::t('steroids', 'Запомнить меня'),
                'appType' => 'boolean'
            ],
            'reCaptcha' => [
                'label' => Yii::t('steroids', 'Я не робот')
            ],
            'google2faEnable' => [
                'label' => Yii::t('steroids', 'Google 2FA'),
                'appType' => 'boolean',
            ],
            'google2faCode' => [
                'label' => Yii::t('app', 'Google 2FA Code')
            ],
        ];
    }
}
