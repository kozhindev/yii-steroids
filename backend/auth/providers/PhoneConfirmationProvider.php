<?php

namespace steroids\auth\providers;

use steroids\modules\user\models\User;
use yii\base\BaseObject;

class PhoneConfirmationProvider extends BaseObject
{
    /**
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function start($user)
    {
        $user->confirmKey = \Yii::$app->security->generateRandomString(4); // TODO Generate numbers
        $user->saveOrPanic();

        // TODO Send sms
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
