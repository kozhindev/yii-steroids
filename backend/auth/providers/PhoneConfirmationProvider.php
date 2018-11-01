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
     * @var string
     */
    public $keyAttribute = 'phoneConfirmKey';

    /**
     * @var string
     */
    public $timeAttribute = 'phoneConfirmTime';

    /**
     * @var int
     */
    public $codeLength = 4;

    /**
     * @var bool
     */
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

        $user->setAttribute($this->keyAttribute, static::generateRandomNumbers($this->codeLength));
        $user->saveOrPanic();

        if (!$this->standalone) {
            $message = \Yii::t('steroids', 'Проверочный код: {code}', ['code' => $user->getAttribute($this->keyAttribute)]);
            $this->sms->send($user->phone, $message);
        }
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
            $this->keyAttribute => $code,
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

        $user->setAttribute($this->keyAttribute, null);
        $user->setAttribute($this->timeAttribute, date('Y-m-d H:i:s'));
        $user->saveOrPanic();
    }
}
