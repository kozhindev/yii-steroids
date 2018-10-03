<?php

namespace steroids\validators;

use PragmaRX\Google2FA\Google2FA;
use steroids\base\Model;
use steroids\base\MultiFactorAuthValidator;

class GoogleAuthenticatorMfaValidator extends MultiFactorAuthValidator
{
    /**
     * @inheritdoc
     */
    public $skipOnEmpty = false;

    /**
     * @var string
     */
    public $secretKeyAttribute = 'google2faSecretKey';

    /**
     * @var string
     */
    public $enableAttribute = 'google2faEnable';

    /**
     * @var string
     */
    public $securityAttribute = 'google2faCode';

    /**
     * @param string $secretKey
     * @param string $code
     * @return bool
     */
    public static function verify($secretKey, $code)
    {
        if ($code) {
            $code = preg_replace('/[^0-9]/', '', $code);
        }
        return $secretKey && $code && (new Google2FA())->verifyKey($secretKey, $code);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = \Yii::t('steroids', 'Неверный код');
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate($model)
    {
        if ($this->identity && $this->identity instanceof Model) {
            /** @var Model $model */
            $model = $this->identity;

            // Only for user with enabled google 2fa
            return $model->hasAttribute($this->enableAttribute)
                && $model->hasAttribute($this->secretKeyAttribute)
                && $model->getAttribute($this->enableAttribute);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $code = \Yii::$app->request->post($this->securityAttribute);
        if (!$code) {
            $model->requireSecurityComponent([
                'component' => 'GoogleAuthenticatorField',
                'attribute' => $this->securityAttribute,
            ]);
        } elseif (!static::verify($this->identity->{$this->secretKeyAttribute}, $code)) {
            $model->requireSecurityComponent([
                'component' => 'GoogleAuthenticatorField',
                'attribute' => $this->securityAttribute,
                'error' => \Yii::$app->getI18n()->format($this->message, [
                    'attribute' => $model->getAttributeLabel($attribute),
                ], \Yii::$app->language),
            ]);
        }
    }
}
