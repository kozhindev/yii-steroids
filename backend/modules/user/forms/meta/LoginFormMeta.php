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

    public function rules()
    {
        return [
            [['login', 'reCaptcha'], 'string', 'max' => 255],
            [['login', 'password'], 'required'],
            ['password', 'string', 'min' => 1,'max' => 255],
            ['rememberMe', 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'login' => [
                'label' => Yii::t('app', 'Логин или email'),
                'isRequired' => true
            ],
            'password' => [
                'label' => Yii::t('app', 'Пароль'),
                'appType' => 'password',
                'isRequired' => true
            ],
            'rememberMe' => [
                'label' => Yii::t('app', 'Запомнить вход'),
                'appType' => 'boolean'
            ],
            'reCaptcha' => [
                'label' => Yii::t('app', 'Я не робот')
            ]
        ];
    }
}
