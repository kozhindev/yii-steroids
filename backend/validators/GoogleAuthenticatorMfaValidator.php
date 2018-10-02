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
                && $model->{$this->enableAttribute};
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $code = $model->$attribute;
        if (!static::verify($this->identity->{$this->secretKeyAttribute}, $code)) {
            if ($code === null) {
                $model->addSecurityFields([
                    'label' => \Yii::t('steroids', 'Введите код подтверждения'),
                    'attribute' => $this->securityAttribute,
                    'component' => 'InputField',
                    'placeholder' => '000 000',
                ]);
            } else {
                $this->addError($model, $attribute, \Yii::$app->getI18n()->format($this->message, [
                    'attribute' => $model->getAttributeLabel($attribute),
                ], \Yii::$app->language));
            }
        }
    }
}
