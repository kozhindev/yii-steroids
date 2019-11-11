<?php

namespace steroids\auth\forms;

use steroids\auth\AuthModule;
use steroids\auth\base\BaseAuthProvider;
use steroids\auth\forms\meta\SocialLoginFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\auth\models\AuthSocial;

class SocialLoginForm extends SocialLoginFormMeta
{
    /**
     * @var array
     */
    public $socialParams = [];

    /**
     * @var BaseAuthProvider
     */
    public $provider;

    /**
     * @var AuthSocial
     */
    public $social;

    /**
     * @var string
     */
    public $accessToken;

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'social' => [
                '*',
                'uid',
                'isEmailNeed',
            ],
            'accessToken',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
            ['socialName', function($attribute) {
            // TODO
                /*$this->provider = AuthModule::getInstance()->getProvider($this->$attribute);
                if (!$this->provider) {
                    $this->addError($attribute, \Yii::t('steroids', 'Такой провайдер не найден'));
                }*/
            }],
            ['socialParams', 'safe'],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function login()
    {
        if ($this->validate()) {
            // Auth via provider
            $profile = $this->provider->auth($this->socialParams);

            // Find or create AuthSocial
            $this->social = AuthSocial::findOrCreate($this->socialName, $profile);

            $user = \Yii::$app->user->model;
            if ($user) {
                // Connect
                $this->social->appendUser($user->email);

            } elseif (!$this->social->isEmailNeed) {
                // Create
                \Yii::$app->user->login($this->social->user);
            }

            if (!\Yii::$app->user->isGuest) {
                $this->accessToken = \Yii::$app->user->accessToken;
            }

        }
    }
}
