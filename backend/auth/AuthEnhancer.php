<?php

namespace steroids\auth;

use Yii;
use steroids\auth\providers\BaseProvider;
use steroids\auth\providers\EmailConfirmationProvider;
use steroids\auth\providers\PhoneConfirmationProvider;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @property-read EmailConfirmationProvider $emailProvider
 * @property-read PhoneConfirmationProvider $phoneProvider
 */
class AuthEnhancer extends Component
{
    const PROVIDER_EMAIL = 'email';
    const PROVIDER_PHONE = 'phone';

    /**
     * @var array
     */
    public $providers = [];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->providers = array_merge(
            [
                self::PROVIDER_EMAIL => [
                    'class' => EmailConfirmationProvider::class,
                ],
                self::PROVIDER_PHONE => [
                    'class' => PhoneConfirmationProvider::class,
                ],
            ],
            $this->providers
        );
    }

    public function getEmailProvider()
    {
        return $this->getProvider(self::PROVIDER_EMAIL);
    }

    public function getPhoneProvider()
    {
        return $this->getProvider(self::PROVIDER_PHONE);
    }

    /**
     * @param string $name
     * @return BaseProvider|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider($name)
    {
        if (!ArrayHelper::keyExists($name, $this->providers)) {
            return null;
        }

        if (is_array($this->providers[$name])) {
            $this->providers[$name] = Yii::createObject($this->providers[$name]);
        }
        return $this->providers[$name];
    }

}
