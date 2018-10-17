<?php

namespace steroids\modules\user\forms;

use steroids\validators\GoogleAuthenticatorMfaValidator;
use Yii;
use steroids\validators\ReCaptchaMfaValidator;
use steroids\modules\user\forms\meta\LoginFormMeta;
use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class LoginForm extends LoginFormMeta
{
    /**
     * @var User|bool
     */
    public $user;

    /*public $responseModel;

    public function responseModelClass()
    {
        return UserModule::getInstance()->modelsMap['User'];
    }

    public function responseFields()
    {
        return [
            'id',
            'role',
            'name',
        ];
    }*/

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['password', function ($attribute) {
                /** @var User $modelClass */
                $modelClass = UserModule::getInstance()->modelsMap['User'];

                $this->user = $modelClass::find()
                    ->where([
                        'or',
                        ['email' => $this->login],
                        ['login' => $this->login],
                    ])
                    ->limit(1)
                    ->one();
                if (!$this->user || !$this->user->validatePassword($this->$attribute)) {
                    $this->$attribute = null;
                    $this->addError($attribute, Yii::t('steroids', 'Неверный логин или пароль'));
                }
            }],
            ['login', function ($attribute) {
                if ($this->user && $this->user->emailConfirmKey) {
                    $this->addError($attribute, \Yii::t('steroids', 'Email не подтвержден. Проверьте почту или восстановите пароль'));
                }
            }],
            ['reCaptcha', ReCaptchaMfaValidator::class, 'when' => function () {
                return UserModule::getInstance()->enableCaptcha;
            }],
            ['google2faCode', GoogleAuthenticatorMfaValidator::class],
        ]);
    }

    /**
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }
}
