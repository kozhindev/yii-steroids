<?php

namespace steroids\auth;

use yii\base\Component;

class BaseTwoFactorWorkflow extends Component
{
    public $providers = [];

    /**
     * @param $name
     * @return BaseTwoFactorProvider
     */
    public function getProvider($name)
    {
        return new BaseTwoFactorProvider();
    }
}
