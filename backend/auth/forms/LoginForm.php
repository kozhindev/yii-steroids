<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\LoginFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\auth\UserInterface;

class LoginForm extends LoginFormMeta
{
    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @var string
     */
    public $accessToken;

    public function fields()
    {
        return [
            'accessToken',
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
            ['password', function ($attribute) {
                /** @var UserInterface $userClass */
                $userClass = \Yii::$app->user->identityClass;

                $this->user = $userClass::findByEmail($this->email);
                if (!$this->user || !$this->user->validatePassword($this->$attribute)) {
                    $this->$attribute = null;
                    $this->addError($attribute, \Yii::t('steroids', 'Неверный логин или пароль'));
                }
            }],
            ['email', function ($attribute) {
                $isConfirmed = AuthConfirm::find()
                    ->where([
                        'userId' => $this->user->getId(),
                        'isConfirmed' => true
                    ])
                    ->exists();
                if (!$isConfirmed) {
                    $this->addError($attribute, \Yii::t('steroids', 'Email не подтвержден. Проверьте почту или восстановите пароль'));
                }
            }],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function login()
    {
        if ($this->validate()) {
            \Yii::$app->user->login($this->user);
            $this->accessToken = \Yii::$app->user->accessToken;
        }
    }
}
