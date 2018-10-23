<?php

namespace steroids\modules\user\forms\meta;

use \Yii;
use steroids\base\FormModel;
use steroids\validators\PhoneValidator;

abstract class RegistrationPhoneFormMeta extends FormModel
{
    public $phone;

    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', PhoneValidator::class],
        ];
    }

    public static function meta()
    {
        return [
            'phone' => [
                'label' => Yii::t('steroids', 'Телефон'),
                'appType' => 'phone',
                'isRequired' => true
            ]
        ];
    }
}
