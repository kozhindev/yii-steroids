<?php

namespace app\auth\providers;

use app\auth\base\BaseAuthProvider;
use VK\Client\VKApiClient;
use Exception;
use VK\OAuth\VKOAuth;
use yii\helpers\Url;

class VkSocialProvider extends BaseAuthProvider
{
    const API_VERSION = '5.92';

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
    public function getProfileData()
    {
        if (!$this->accessToken) {
            throw new Exception('VK API call isn\'t possible without token');
        }

        if (!$this->_profileData) {
            $this->accessToken = $this->exchangeTempCodeOnAccessToken($this->accessToken);

            $accountInfo = $this->api->users()->get($this->accessToken);

            $this->_profileData = [
                'externalId' => (string)$accountInfo[0]['id'],
                'name' => $accountInfo[0]['first_name'] . ' ' . $accountInfo[0]['last_name'],
            ];
        }

        return $this->_profileData;
    }

    /**
     * Actually, the code we get from VK isn't an access token, but just temporal code,
     * which could be exchanged on real access token.
     * That's what this method does.
     *
     * @param string $tempCode
     * @return mixed
     *
     * @throws \VK\Exceptions\VKClientException
     * @throws \VK\Exceptions\VKOAuthException
     */
    /*protected function exchangeTempCodeOnAccessToken($tempCode)
    {
        $oauthApi = new VKOAuth();

        $response = $oauthApi->getAccessToken(
            $this->apiId,
            $this->apiSecret,
            Url::to(['/auth/auth/modal-proxy', 'version' => 'v2'], true),
            $tempCode
        );

        return $response['access_token'];
    }

    protected function getApi()
    {
        if (!$this->_api) {
            $this->_api = new VKApiClient(self::API_VERSION);
        }

        return $this->_api;
    }*/
}
