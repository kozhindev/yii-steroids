<?php

namespace steroids\auth\providers;


class EmailRegistrationProvider extends BaseTwoFactorProvider
{
    public $contextUser;

    public function start()
    {
        $this->contextUser->emailConfirmKey=\Yii::$app->security->generateRandomString();
        $this->contextUser->saveOrPanic();

        \Yii::$app->mailer->compose(\Yii::$app->view->findOverwriteView('@steroids/modules/user/mail/registration'), [
            'user' => $this->contextUser
        ])
            ->setTo($this->contextUser->email)
            ->send();

        \Yii::$app->session->setFlash('success', \Yii::t('steroids', 'Подтвердите ваш e-mail для завершения регистрации'));
    }

    public function end()
    {

    }
}