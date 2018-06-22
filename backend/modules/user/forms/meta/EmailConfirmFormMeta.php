<?php

namespace steroids\modules\user\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class EmailConfirmFormMeta extends FormModel
{
    public $email;
    public $code;

    public function rules()
    {
        return [
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'code'], 'required'],
            ['code', 'string', 'max' => '32'],
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
                'isRequired' => true,
                'stringLength' => '32'
            ]
        ];
    }
}
