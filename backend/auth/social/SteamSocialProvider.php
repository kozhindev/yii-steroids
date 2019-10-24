<?php

namespace app\auth\providers;

use app\auth\base\BaseAuthProvider;
use app\auth\base\SocialProfile;
use app\auth\exceptions\SocialAuthException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * For register api key go to https://steamcommunity.com/dev/apikey
 */
class SteamSocialProvider extends BaseAuthProvider
{
    public $apiKey = '';

    public function getClientConfig()
    {
        return [];
    }

    public function auth(array $params)
    {
        $openid = new \LightOpenID($params['openid.return_to']);
        $openid->returnUrl = $params['openid.return_to'];
        $openid->data = [];
        foreach ($params as $key => $value) {
            $openid->data[str_replace('openid.', 'openid_', $key)] = $value;
        }

        $steamId = preg_replace('/^.+[^0-9]([0-9]+)$/', '$1', $openid->data['openid_identity']);
        if (!$steamId) {
            throw new SocialAuthException('Cannot parse steam id');
        }

        if (!$openid->validate()) {
            throw new SocialAuthException('OpenID validation return false');
        }

        $profile = $this->fetchProfile($steamId);
        if (!$profile) {
            throw new SocialAuthException('Cannot fetch profile');
        }

        return $profile;
    }

    protected function fetchProfile($steamId)
    {
        $data = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?' . http_build_query([
            'key' => $this->apiKey,
            'steamids' => $steamId,
        ]));
        $player = ArrayHelper::getValue(Json::decode($data), 'response.players.0');
        if (!$player) {
            return null;
        }

        return new SocialProfile([
            'id' => $steamId,
            'name' => $player['personaname'],
            'avatarUrl' => $player['avatarfull'],
        ]);
    }
}
