<?php

namespace steroids\modules\user\forms;

use steroids\modules\user\forms\meta\PasswordResetRequestFormMeta;
use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class PasswordResetRequestForm extends PasswordResetRequestFormMeta
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'exist',
                'targetClass' => User::className(),
                'message' => \Yii::t('steroids', 'Нет пользователя с таким email адресом'),
            ],
        ]);
    }

    public function send()
    {
        if ($this->validate()) {

            /** @var User $modelClass */
            $modelClass = UserModule::getInstance()->modelsMap['User'];

            $user = $modelClass::findOrPanic([
                'email' => $this->email,
            ]);
            if ($user) {
                $user->confirmKey = \Yii::$app->security->generateRandomString();
                $user->saveOrPanic();

                \Yii::$app->mailer->compose(\Yii::$app->view->findOverwriteView('@steroids/modules/user/mail/resetPassword'), [
                    'user' => $user
                ])
                    ->setTo($this->email)
                    ->send();
            }

            return true;
        }
        return false;
    }
}
