<?php

namespace steroids\auth\providers;

use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class EmailConfirmationProvider extends BaseProvider
{
    /**
     * @param User $user
     * @param string|null $view
     * @throws \yii\base\Exception
     */
    public function start($user, $view = null)
    {
        $user->confirmKey = \Yii::$app->security->generateRandomString();
        $user->saveOrPanic();

        $view = $view ?: \Yii::$app->view->findOverwriteView('@steroids/modules/user/mail/registration');
        \Yii::$app->mailer
            ->compose($view, [
                'user' => $user
            ])
            ->setTo($user->email)
            ->send();
    }

    /**
     * @param string $email
     * @param string $code
     * @return User|null
     * @throws \yii\base\Exception
     */
    public function check($email, $code)
    {
        /** @var User $modelClass */
        $modelClass = UserModule::getInstance()->modelsMap['User'];
        return $modelClass::findOne([
            'email' => $email,
            'confirmKey' => $code,
        ]);
    }

    /**
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function end($user)
    {
        $user->confirmKey = null;
        $user->saveOrPanic();
    }
}
