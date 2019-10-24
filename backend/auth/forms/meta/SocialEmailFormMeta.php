<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class SocialEmailFormMeta extends FormModel
{
    public $uid;
    public $email;

    public function rules()
    {
        return [
            [['uid', 'email'], 'string', 'max' => 255],
            [['uid', 'email'], 'required'],
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
            ]
        ];
    }
}
