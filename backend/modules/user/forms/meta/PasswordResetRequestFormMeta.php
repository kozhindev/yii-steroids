<?php

namespace steroids\modules\user\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class PasswordResetRequestFormMeta extends FormModel
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'required'],
        ];
    }

    public static function meta()
    {
        return [
            'email' => [
                'label' => Yii::t('steroids', 'Email'),
                'appType' => 'email',
                'isRequired' => true
            ]
        ];
    }
}
