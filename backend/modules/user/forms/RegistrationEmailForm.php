<?php

namespace steroids\modules\user\forms;

use steroids\modules\user\forms\meta\RegistrationEmailFormMeta;
use steroids\modules\user\models\User;

class RegistrationEmailForm extends RegistrationEmailFormMeta
{
    /**
     * @var User
     */
    public $user;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'unique', 'targetClass' => User::class],
            ['password', 'compare', 'compareAttribute' => 'passwordAgain'],
        ]);
    }

    public function register()
    {
        if ($this->validate()) {
            $this->user = new User();
            $this->user->role = 'user'; // TODO
            $this->user->passwordHash = \Yii::$app->security->generatePasswordHash($this->password);
            $this->user->attributes = $this->getAttributes();
            $this->user->saveOrPanic();

            return true;
        }

        return false;
    }
}
