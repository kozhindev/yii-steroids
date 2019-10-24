<?php

namespace steroids\notifier;

use Yii;
use steroids\notifier\providers\BaseProvider;
use steroids\notifier\providers\MailerNotifierProvider;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Notifier extends Component
{
    const PROVIDER_MAILER = 'mailer';
    //const PROVIDER_SMSRU = 'smsru';
    //const PROVIDER_EASYSMS = 'easysms';

    /**
     * @var string
     */
    public $defaultProviderName = self::PROVIDER_MAILER;

    /**
     * @var array
     */
    public $providers = [];

    /**
     * @var array
     */
    public $templates = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->providers = ArrayHelper::merge(
            [
                self::PROVIDER_MAILER => [
                    'class' => MailerNotifierProvider::class,
                ],
            ],
            $this->providers
        );
    }

    /**
     * @param string $providerName
     * @param string $templateName
     * @param array $params
     * @param string|null $language
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function send($providerName, $templateName, $params = [], $language = null)
    {
        $providerName = $providerName ?: $this->defaultProviderName;
        $language = $language ?: Yii::$app->language;

        // Get provider
        $provider = $this->getProvider($providerName);
        if (!$provider) {
            throw new Exception('Not found notifier provider "' . $providerName . '".');
        }

        // Get template path
        $templatePath = ArrayHelper::getValue($provider->templates, $templateName)
            ?: ArrayHelper::getValue($this->templates, $templateName);
        if (!$templatePath) {
            throw new Exception('Not found notifier template "' . $templateName . '".');
        }

        $provider->send($templatePath, $params, $language);
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
