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

    public $useSession = true;

    /**
     * @var string|array
     */
    public $sms = 'sms';

    public function init()
    {
        parent::init();

        // Init SMS Component
        if (is_array($this->sms)) {
            $this->sms = \Yii::createObject($this->sms);
        } elseif (is_string($this->sms)) {
            $this->sms = \Yii::$app->get($this->sms);
        }
    }

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
     * @param User $user
     * @throws \yii\base\Exception
     */
    public function start($user)
    {
        if ($this->useSession) {
            \Yii::$app->session->set(self::SESSION_PHONE_KEY, $user->phone);
        }

        $user->confirmKey = static::generateRandomNumbers($this->codeLength);
        $user->saveOrPanic();

        $message = \Yii::t('steroids', 'Проверочный код: {code}', ['code' => $user->confirmKey]);
        $this->sms->send($user->phone, $message);
    }

    /**
     * @param string $code
     * @param string|null $phone
     * @return User|null
     * @throws \yii\base\Exception
     */
    public function check($code, $phone = null)
    {
        if ($this->useSession) {
            $phone = \Yii::$app->session->get(self::SESSION_PHONE_KEY);
        }

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
        if ($this->useSession) {
            \Yii::$app->session->remove(self::SESSION_PHONE_KEY);
        }

        $user->confirmKey = null;
        $user->confirmTime = date('Y-m-d H:i');
        $user->saveOrPanic();
    }
}