<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\RegistrationFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\auth\UserInterface;
use steroids\base\Model;
use steroids\validators\PasswordValidator;

class RegistrationForm extends RegistrationFormMeta
{
    /**
     * @var UserInterface|Model
     */
    public $user;

    public function rules()
    {
        /** @var UserInterface $userClass */
        $userClass = \Yii::$app->user->identityClass;

        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
            ['email', 'unique', 'targetClass' => $userClass],
            ['name', 'unique', 'targetClass' => $userClass, 'targetAttribute' => 'username'],
            ['password', PasswordValidator::class],
            ['password', 'compare', 'compareAttribute' => 'passwordAgain'],
        ]);
    }

    public function register()
    {
        /** @var UserInterface $userClass */
        $userClass = \Yii::$app->user->identityClass;

        if ($this->validate()) {
            $transaction = static::getDb()->beginTransaction();
            try {
                // Save user
                $this->user = new $userClass();
                $this->user->attributes = [
                    'role' => \Yii::$app->user->defaultRole,
                    'passwordHash' => \Yii::$app->security->generatePasswordHash($this->password),
                    'email' => $this->email,
                    'username' => $this->name,
                ];
                $this->user->saveOrPanic();

                // Confirm email
                AuthConfirm::create($this->user->email);

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }
}
