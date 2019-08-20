<?php

namespace steroids\modules\user\forms\meta;

use \Yii;
use steroids\base\FormModel;

abstract class PhoneConfirmFormMeta extends FormModel
{
    public $code;

    public function rules()
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'max' => '32'],
        ];
    }

    public static function meta()
    {
        return [
            'code' => [
                'label' => Yii::t('steroids', 'Код'),
                'isRequired' => true,
                'stringLength' => '32'
            ]
        ];
    }
}
