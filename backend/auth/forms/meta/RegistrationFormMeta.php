<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class RegistrationFormMeta extends FormModel
{
    public $email;
    public $password;
    public $passwordAgain;
    public $name;
    public $birthdate;

    public function rules()
    {
        return [
            [['email', 'name'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'password', 'passwordAgain', 'name', 'birthdate'], 'required'],
            [['password', 'passwordAgain'], 'string', 'min' => 1,'max' => 255],
            ['birthdate', 'date', 'format' => 'php:Y-m-d'],
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
                'label' => Yii::t('steroids', 'Имя'),
                'isRequired' => true
            ],
            'birthdate' => [
                'label' => Yii::t('steroids', 'Дата рождения'),
                'appType' => 'date',
                'isRequired' => true
            ]
        ];
    }
}
