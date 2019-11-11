<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class RegistrationConfirmFormMeta extends FormModel
{
    public $email;
    public $code;

    public function rules()
    {
        return [
            [['email', 'code'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'code'], 'required'],
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
            'code' => [
                'label' => Yii::t('steroids', 'Код'),
                'isRequired' => true
            ]
        ];
    }
}
