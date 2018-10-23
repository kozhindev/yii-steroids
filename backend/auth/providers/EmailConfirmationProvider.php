<?php

namespace steroids\auth\providers;

use steroids\modules\user\models\User;
use yii\base\BaseObject;

class EmailConfirmationProvider extends BaseObject
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
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function end($user)
    {
        $user->confirmKey = null;
        $user->saveOrPanic();
    }
}
