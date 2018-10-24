<?php

namespace steroids\auth\providers;

use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;
use steroids\sms\BaseSmsGateway;

/**
 * @property-read BaseSmsGateway $sms
 */
class PhoneConfirmationProvider extends BaseProvider
{
    const SESSION_PHONE_KEY = 'auth_phone_confirmation_phone';

    /**
     * @var int
     */
    public $codeLength = 4;

    /**
     * @var string|array
     */
    public $sms = 'sms';

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomNumbers($length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * @return BaseSmsGateway
     * @throws \yii\base\InvalidConfigException
     */
    public function getSms()
    {
        if (is_array($this->sms)) {
            $this->sms = \Yii::createObject($this->sms);
        } elseif (is_string($this->sms)) {
            return \Yii::$app->get($this->sms);
        }
        return $this->sms;
    }

    /**
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function start($user)
    {
        \Yii::$app->session->set(self::SESSION_PHONE_KEY, $user->phone);

        $user->confirmKey = static::generateRandomNumbers($this->codeLength);
        $user->saveOrPanic();

        $message = \Yii::t('steroids', 'Проверочный код: {code}', ['code' => $user->confirmKey]);
        $this->sms->send($user->phone, $message);
    }

    /**
     * @param string $code
     * @return User|null
     * @throws \yii\base\Exception
     */
    public function check($code)
    {
        $phone = \Yii::$app->session->get(self::SESSION_PHONE_KEY);
        if (!$phone) {
            return null;
        }

        /** @var User $modelClass */
        $modelClass = UserModule::getInstance()->modelsMap['User'];
        return $modelClass::findOne([
            'phone' => $phone,
            'confirmKey' => $code,
        ]);
    }

    /**
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function end($user)
    {
        \Yii::$app->session->remove(self::SESSION_PHONE_KEY);

        $user->confirmKey = null;
        $user->saveOrPanic();
    }
}
