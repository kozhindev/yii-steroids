<?php

namespace steroids\modules\user\forms\meta;

use steroids\base\FormModel;

abstract class PasswordResetChangeFormMeta extends FormModel
{
    public $token;
    public $newPassword;
    public $newPasswordAgain;

    public function rules()
    {
        return [
            ['token', 'string', 'max' => '32'],
            [['token', 'newPassword', 'newPasswordAgain'], 'required'],
            [['newPassword', 'newPasswordAgain'], 'string', 'min' => 1,'max' => 255],
        ];
    }

    public static function meta()
    {
        return [
            'token' => [
                'isRequired' => true,
                'stringLength' => '32'
            ],
            'newPassword' => [
                'appType' => 'password',
                'isRequired' => true
            ],
            'newPasswordAgain' => [
                'appType' => 'password',
                'isRequired' => true
            ]
        ];
    }
}
