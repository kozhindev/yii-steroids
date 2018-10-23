<?php

namespace steroids\auth;

use steroids\auth\providers\BaseTwoFactorProvider;
use steroids\auth\providers\EmailRegistrationProvider;
use yii\base\Component;
use yii\web\IdentityInterface;

class BaseTwoFactorWorkflow extends Component
{
    public $providers = [];

    /**
     * @param $name
     * @return BaseTwoFactorProvider
     */
    public function getProvider($name)
    {
        switch ($name) {
            case 'email':
                return new EmailRegistrationProvider();
            default:
                return null;
        }
    }

    public function getProviderForModel($model)
    {
        foreach ($this->providers as $providerName) {
            $provider = $this->getProvider($providerName);

            if (!$provider || !$provider->enable) {
                continue;
            }

            if ($model instanceof IdentityInterface && !$provider->isEnableFor($model)) {
                continue;
            }

            return $provider;
        }

        return null;
    }
}
