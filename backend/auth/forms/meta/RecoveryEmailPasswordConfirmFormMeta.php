<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class RecoveryEmailPasswordConfirmFormMeta extends FormModel
{
    public $email;
    public $code;
    public $newPassword;
    public $newPasswordAgain;

    public function rules()
    {
        return [
            [['email', 'code'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'code', 'newPassword', 'newPasswordAgain'], 'required'],
            [['newPassword', 'newPasswordAgain'], 'string', 'min' => 1,'max' => 255],
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
            ],
            'newPassword' => [
                'label' => Yii::t('steroids', 'Новый пароль'),
                'appType' => 'password',
                'isRequired' => true
            ],
            'newPasswordAgain' => [
                'label' => Yii::t('steroids', 'Повтор пароля'),
                'appType' => 'password',
                'isRequired' => true
            ]
        ];
    }
}
