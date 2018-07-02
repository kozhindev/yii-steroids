<?php

namespace steroids\validators;


use PragmaRX\Google2FA\Google2FA;
use steroids\modules\user\forms\LoginForm;
use yii\validators\Validator;
use yii\web\Response;
use yii\widgets\ActiveForm;

class GoogleAuthenticator extends Validator
{
    public $skipOnEmpty = false;
    public $secretKeyAttribute = 'google2faSecretKey';
    public $google2faFlag = 'google2faEnable';

    public static function verify($secretKey, $code)
    {
        if ($code) {
            $code = preg_replace('/[^0-9]/', '', $code);
        }
        return $secretKey && $code && (new Google2FA())->verifyKey($secretKey, $code);
    }

    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = \Yii::t('steroids', 'Неверный код');
        }
    }

    /**
     * @param LoginForm $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $user = $model->user;
        $secretKeyAttribute = $this->secretKeyAttribute;
        $google2faFlag = $this->google2faFlag;

        if ($user && $user->$google2faFlag) {
            $code = $model->$attribute;
            if (!static::verify($user->$secretKeyAttribute, $code)) {
                if ($code === null) {
                    $model->addSecurityFields([
                        'label' => $model->getAttributeLabel($attribute),
                        'attribute' => $attribute,
                        'component' => 'InputField'
                    ]);
                } else {
                    $this->addError($model, $attribute, \Yii::$app->getI18n()->format($this->message, [
                        'attribute' => $model->getAttributeLabel($attribute),
                    ], \Yii::$app->language));
                }
            }
        }
    }
}
