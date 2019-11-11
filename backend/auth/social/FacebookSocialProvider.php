<?php

namespace app\auth\providers;

use app\auth\base\BaseAuthProvider;
use Facebook\Facebook;
use Exception;

class FacebookSocialProvider extends BaseAuthProvider
{
    /**
     * @var string
     */
    public $clientId;

    /**
     * @var string
     */
    public $clientSecret;

    public function auth(array $params)
    {

    }

    public function getClientConfig()
    {
        return [
            'clientId' => $this->clientId,
        ];
    }

    /*
    public function getProfileData() {
        if (!$this->accessToken) {
            throw new Exception('FB API call isn\'t possible without token');
        }

        if (!$this->_profileData) {
            $response = $this->getApi()->get(
                "/me/?fields=name,email",
                $this->accessToken
            );

            $graphUser = $response->getGraphUser();

            $this->_profileData = [
                'externalId' => $graphUser['id'],
                'email' => isset($graphUser['email']) ? $graphUser['email'] : null,
                'name' => $graphUser['name']
            ];
        }

        return $this->_profileData;
    }

    protected function getApi()
    {
        if (!$this->_api) {
            $this->_api = new Facebook([
                'app_id' => $this->app_id,
                'app_secret' => $this->app_secret
            ]);
        }

        return $this->_api;
    }*/
}
