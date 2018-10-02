<?php

namespace steroids\validators;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\Validator;

class ReCaptchaMethod extends Validator
{
    /**
     * @var string
     */
    public $url = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var string
     */
    public $sessionKey = 'recaptcha-response';

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
            $this->message = Yii::t('steroids', 'Проверка не пройдена');
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