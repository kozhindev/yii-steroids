<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class RecoveryEmailPasswordFormMeta extends FormModel
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
