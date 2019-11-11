<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\RecoveryEmailPasswordFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\auth\UserInterface;

class RecoveryEmailPasswordForm extends RecoveryEmailPasswordFormMeta
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
        ]);
    }

    public function send()
    {
        if ($this->validate()) {
            /** @var UserInterface $userClass */
            $userClass = \Yii::$app->user->identityClass;

            $user = $userClass::findByEmail($this->email);
            if ($user) {
                AuthConfirm::create($user->email);
            }
            return true;
        }
        return false;
    }
}
