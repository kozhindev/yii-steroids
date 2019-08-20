<?php

namespace steroids\modules\user\forms;

use steroids\modules\user\forms\meta\RegistrationPhoneFormMeta;
use steroids\modules\user\models\User;

class RegistrationPhoneForm extends RegistrationPhoneFormMeta
{
    /**
     * @var User
     */
    public $user;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['phone', 'unique', 'targetClass' => User::class],
        ]);
    }

    public function register()
    {
        if ($this->validate()) {
            $this->user = new User();
            $this->user->role = 'user'; // TODO
            $this->user->attributes = $this->getAttributes();
            $this->user->saveOrPanic();

            return true;
        }

        return false;
    }
}
