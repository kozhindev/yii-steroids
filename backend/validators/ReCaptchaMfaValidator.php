<?php

namespace steroids\validators;

use Yii;
use steroids\base\MultiFactorAuthValidator;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

class ReCaptchaMfaValidator extends MultiFactorAuthValidator
{
    /**
     * @var string
     */
    public $url = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var string
     */
    public $sessionKey = 'mfa-recaptcha-response';

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $securityAttribute = 'reCaptcha';

    /**
     * @var bool
     */
    protected $isValid;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->secret) {
            throw new InvalidConfigException('Wrong validator config: secret code is required.');
        }

        if ($this->message === null) {
            $this->message = Yii::t('steroids', 'Проверка не пройдена');
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate($model)
    {
        // Only for guests
        return \Yii::$app->user->isGuest;
    }

    public function validateAttribute($model, $attribute)
    {
        $code = \Yii::$app->request->post($this->securityAttribute);
        if (!$code) {
            $model->requireSecurityComponent([
                'component' => 'ReCaptchaField',
                'attribute' => $this->securityAttribute,
                'error' => '',
            ]);
        } elseif ($this->validateValue($code)) {
            $model->requireSecurityComponent([
                'component' => 'ReCaptchaField',
                'attribute' => $this->securityAttribute,
                'error' => \Yii::$app->getI18n()->format($this->message, [
                    'attribute' => $model->getAttributeLabel($attribute),
                ], \Yii::$app->language),

            ]);
        }
    }

    /**
     * @param mixed $value
     * @return array|null
     * @throws Exception
     */
    protected function validateValue($value)
    {
        if ($this->isValid === null) {
            // Remote check
            $this->isValid = $this->sendRequest($value);

            // Try previous key
            if (!$this->isValid) {
                $this->isValid = $this->sendRequest(Yii::$app->session->get($this->sessionKey));
            }

            // Save key
            if ($this->isValid) {
                Yii::$app->session->set($this->sessionKey, $value);
            }
        }

        return $this->isValid ? null : [$this->message, []];
    }

    /**
     * @param $value
     * @return bool
     * @throws Exception
     */
    protected function sendRequest($value)
    {
        if (!$value) {
            return false;
        }

        $url = $this->url . '?' . http_build_query([
                'secret' => $this->secret,
                'response' => $value,
                'remoteip' => Yii::$app->request->userIP,
            ]);

        $response = @file_get_contents($url);
        if ($response === false) {
            throw new Exception('Unable connection to the captcha server.');
        }

        $data = Json::decode($response, true);
        if (!isset($data['success'])) {
            throw new Exception('Invalid recaptcha verify response.');
        }

        return (bool)$data['success'];
    }
}