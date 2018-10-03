<?php

namespace steroids\validators;

use steroids\base\Model;
use steroids\base\MultiFactorAuthValidator;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

class EmailConfirmMfaValidator extends MultiFactorAuthValidator
{
    const DEFAULT_LINK_KEY = 'mfaEmailConfirmCode';
    const DEFAULT_SESSION_KEY = 'mfa-email-confirm';

    public $skipOnEmpty = false;

    /**
     * Path to mail view, which will be sent with link to user email
     * @var string
     */
    public $mailView;

    /**
     * @var string
     */
    public $emailAttribute = 'email';

    /**
     * @var string
     */
    public $linkKey = self::DEFAULT_LINK_KEY;

    /**
     * @var string
     */
    public $sessionKey = self::DEFAULT_SESSION_KEY;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public static function beforeAction($event, $params)
    {
        // Append action params from session to body
        $linkKey = ArrayHelper::getValue($params, 'linkKey', self::DEFAULT_LINK_KEY);
        $key = $linkKey && \Yii::$app->request->isGet ? \Yii::$app->request->get($linkKey) : null;
        if ($key) {
            $sessionKey = ArrayHelper::getValue($params, 'sessionKey', self::DEFAULT_SESSION_KEY);
            $data = \Yii::$app->session->get($sessionKey);
            if ($data) {
                \Yii::$app->request->setBodyParams(array_merge(
                    Json::decode($data),
                    \Yii::$app->request->getBodyParams(),
                    [$linkKey => $key]
                ));
            }
        }
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->mailView) {
            throw new InvalidConfigException('Wrong validator config: mailView path is required.');
        }

        if ($this->message === null) {
            $this->message = \Yii::t('steroids', 'Неверный проверочный код');
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

            // Only for logged users
            return $model->hasAttribute($this->emailAttribute);
        }

        return false;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function validateAttribute($model, $attribute)
    {
        $key = \Yii::$app->request->post($this->linkKey);
        if (!$key) {
            // Generate key and link
            $key = \Yii::$app->security->generateRandomString();
            $link = Url::to(
                array_merge(
                    [\Yii::$app->requestedRoute],
                    \Yii::$app->requestedParams,
                    [$this->linkKey => $key]
                ),
                true
            );

            // Send to mail
            $email = $this->identity->{$this->emailAttribute};
            \Yii::$app->mailer
                ->compose($this->mailView, [
                    'user' => $this->identity,
                    'link' => $link,
                ])
                ->setTo($email)
                ->send();

            // Save key in session
            \Yii::$app->session->set($this->sessionKey, $key);

            // Add field
            $model->requireSecurityComponent([
                'component' => 'EmailSecurity',
                'attribute' => $this->linkKey,
                'text' => \Yii::t('steroids', 'На вашу почту {email} отправлено письмо с ссылкой для подтверждения.', [
                    'email' => $email,
                ]),
            ]);
        } else {
            // Validate code from link
            $data = \Yii::$app->session->get($this->sessionKey);
            $exceptedKey = ArrayHelper::getValue(Json::decode($data), $this->linkKey);
            if ($exceptedKey === $key) {
                // Success, clean session
                \Yii::$app->session->remove($this->sessionKey);
            } else {
                // Failure, add error
                $this->addError($model, $attribute, \Yii::$app->getI18n()->format($this->message, [
                    'attribute' => $model->getAttributeLabel($attribute),
                ], \Yii::$app->language));
            }
        }
    }
}