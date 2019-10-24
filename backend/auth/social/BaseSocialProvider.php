<?php

namespace steroids\auth;

use yii\base\Component;

abstract class BaseSocialProvider extends Component
{
    public $name;

    /**
     * @param array $params
     * @return SocialProfile
     */
    public abstract function auth(array $params);

    /**
     * @return array|null
     */
    public function getClientConfig()
    {
        return null;
    }

}
