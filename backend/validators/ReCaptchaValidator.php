<?php

namespace steroids\validators;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\Validator;

class ReCaptchaValidator extends Validator
{
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    const SESSION_KEY = 'recaptcha-response';

    public $skipOnEmpty = false;

    /**
     * @var bool
     */
    protected $isValid;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('app', 'Проверка не пройдена');
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->isValid === null) {
            // Remote check
            $this->isValid = $this->sendRequest($value);

            // Try previous key
            if (!$this->isValid) {
                $this->isValid = $this->sendRequest(Yii::$app->session->get(self::SESSION_KEY));
            }

            // Save key
            if ($this->isValid) {
                Yii::$app->session->set(self::SESSION_KEY, $value);
            }
        }

        return $this->isValid ? null : [$this->message, []];
    }

    /**
     * @param string $value
     * @return mixed
     * @throws Exception
     */
    protected function sendRequest($value)
    {
        if (!$value) {
            return false;
        }

        $url = self::SITE_VERIFY_URL . '?' . http_build_query([
                'secret' => ArrayHelper::getValue(Yii::$app->params, 'googleReCaptcha.secret'),
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