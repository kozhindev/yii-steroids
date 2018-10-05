<?php

namespace steroids\validators;

use steroids\base\MultiFactorAuthValidator;
use yii\base\InvalidConfigException;

class GeetestMfaValidator extends MultiFactorAuthValidator
{
    public $skipOnEmpty = false;

    /**
     * @var string
     */
    public $captchaId;

    /**
     * @var string
     */
    public $captchaPrivateKey;

    /**
     * @var string
     */
    public $captchaUserId = 'test';

    /**
     * @var string
     */
    public $securityAttribute = 'geetest';

    /**
     * @var string
     */
    public $sessionKey = 'mfa-geetest-response';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->captchaId || !$this->captchaPrivateKey) {
            throw new InvalidConfigException('Wrong validator config: captchaId and captchaPrivateKey is required.');
        }

        // Set timeouts for geetest lib
        \GeetestLib::$connectTimeout = 10;
        \GeetestLib::$socketTimeout = 10;

        if ($this->message === null) {
            $this->message = \Yii::t('steroids', 'Проверка не пройдена');
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

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $data = \Yii::$app->request->post($this->securityAttribute);
        $geetestSdk = new \GeetestLib($this->captchaId, $this->captchaPrivateKey);

        if (!$data) {
            // Store geetest status
            \Yii::$app->session->set($this->sessionKey, $geetestSdk->pre_process($this->captchaUserId));

            // Add field
            $model->requireSecurityComponent([
                'component' => 'GeetestField',
                'attribute' => $this->securityAttribute,
                'geetestParams' => $geetestSdk->get_response(),
            ]);
        } else {
            if (\Yii::$app->session->get($this->sessionKey) == 1) {
                $result = $geetestSdk->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $this->captchaUserId);
            } else {
                $result = $geetestSdk->fail_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode']);
            }
            if (!$result) {
                $this->addError($model, $attribute, \Yii::$app->getI18n()->format($this->message, [
                    'attribute' => $model->getAttributeLabel($attribute),
                ], \Yii::$app->language));
            }
        }
    }
}