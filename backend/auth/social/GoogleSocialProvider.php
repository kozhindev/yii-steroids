<?php

namespace app\auth\providers;

use app\auth\base\BaseAuthProvider;
use Google_Client;
use Exception;

/**
 * For get credentials go to https://console.developers.google.com
 * @package app\auth\providers
 */
class GoogleSocialProvider extends BaseAuthProvider
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



/*    public function getProfileData()
    {
        if (!$this->_profileData) {
            $accountInfo = $this->getApi()->verifyIdToken($this->accessToken);

            if (!$accountInfo) {
                throw new Exception('Provided id_token for Google is not verified.');
            } else {
                $this->_profileData = [
                    'externalId' => $accountInfo['sub'],
                    'name' => $accountInfo['name'],
                    'email' => $accountInfo['email']
                ];
            }
        }

        return $this->_profileData;
    }

    protected function getApi()
    {
        if (!$this->_api) {
            $this->_api = new Google_Client(['client_id' => $this->clientId]);
        }

        return $this->_api;
    }*/
}
