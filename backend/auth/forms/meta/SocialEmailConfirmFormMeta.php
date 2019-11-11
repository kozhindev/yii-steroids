<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class SocialEmailConfirmFormMeta extends FormModel
{
    public $uid;
    public $email;
    public $code;

    public function rules()
    {
        return [
            [['uid', 'email', 'code'], 'string', 'max' => 255],
            [['uid', 'email', 'code'], 'required'],
            ['email', 'email'],
        ];
    }

    public static function meta()
    {
        return [
            'uid' => [
                'label' => Yii::t('steroids', 'Uid'),
                'isRequired' => true
            ],
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
