<?php

namespace steroids\auth\forms\meta;

use steroids\base\FormModel;
use steroids\auth\enums\SocialEnum;
use \Yii;

abstract class SocialLoginFormMeta extends FormModel
{
    public $socialName;
    public $email;
    public $code;

    public function rules()
    {
        return [
            ['socialName', 'in', 'range' => SocialEnum::getKeys()],
            ['socialName', 'required'],
            [['email', 'code'], 'string', 'max' => 255],
            ['email', 'email'],
        ];
    }

    public static function meta()
    {
        return [
            'socialName' => [
                'label' => Yii::t('steroids', 'Социальная сеть'),
                'appType' => 'enum',
                'isRequired' => true,
                'enumClassName' => SocialEnum::class
            ],
            'email' => [
                'label' => Yii::t('steroids', 'Email'),
                'appType' => 'email'
            ],
            'code' => [
                'label' => Yii::t('steroids', 'Код')
            ]
        ];
    }
}
