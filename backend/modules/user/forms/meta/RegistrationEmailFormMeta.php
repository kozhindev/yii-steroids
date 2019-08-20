<?php

namespace steroids\modules\user\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class RegistrationEmailFormMeta extends FormModel
{
    public $email;
    public $password;
    public $passwordAgain;
    public $name;

    public function rules()
    {
        return [
            [['email', 'name'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'password', 'passwordAgain'], 'required'],
            [['password', 'passwordAgain'], 'string', 'min' => 1,'max' => 255],
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
            ],
            'passwordAgain' => [
                'label' => Yii::t('steroids', 'Повтор пароля'),
                'appType' => 'password',
                'isRequired' => true
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Имя')
            ]
        ];
    }
}
