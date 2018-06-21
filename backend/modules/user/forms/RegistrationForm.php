<?php

namespace steroids\modules\user\forms;

use steroids\modules\user\forms\meta\RegistrationFormMeta;
use steroids\modules\user\models\User;

class RegistrationForm extends RegistrationFormMeta
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'unique', 'targetClass' => User::className()],
            ['password', 'compare', 'compareAttribute' => 'passwordAgain'],
        ]);
    }

    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->role = 'user';
            $user->passwordHash = \Yii::$app->security->generatePasswordHash($this->password);
            $user->attributes = $this->getAttributes();
            $user->emailConfirmKey=\Yii::$app->security->generateRandomString();
            $user->saveOrPanic();


            \Yii::$app->mailer->compose(\Yii::$app->view->findOverwriteView('@steroids/modules/user/mail/registration'), [
                'user' => $user
            ])
                ->setTo($user->email)
                ->send();

            return true;
        }

        return false;
    }
}
