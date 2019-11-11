<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class LoginFormMeta extends FormModel
{
    public $email;
    public $password;

    public function rules()
    {
        return [
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'password'], 'required'],
            ['password', 'string', 'min' => 1,'max' => 255],
        ];
    }

    public static function meta()
    {
        return [
            'email' => [
                'label' => Yii::t('steroids', 'Email'),
                'appType' => 'email',
                'isRequired' => true
            ],
            'password' => [
                'label' => Yii::t('steroids', 'Пароль'),
                'appType' => 'password',
                'isRequired' => true
            ]
        ];
    }
}
