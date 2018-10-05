<?php

namespace steroids\components;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\MultiFactorAuthValidator;
use steroids\base\WebApplication;
use steroids\validators\EmailConfirmMfaValidator;
use steroids\validators\GeetestMfaValidator;
use steroids\validators\GoogleAuthenticatorMfaValidator;
use steroids\validators\ReCaptchaMfaValidator;
use yii\base\ActionEvent;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * @property-read string[] $validatorNames
 */
class MultiFactorAuthManager extends Component implements BootstrapInterface
{
    const VALIDATOR_GOOGLE_AUTHENTICATOR = 'googleAuthenticator';
    const VALIDATOR_RE_CAPTCHA = 'reCaptcha';
    const VALIDATOR_GEE_TEST = 'geeTest';
    const VALIDATOR_EMAIL_CONFIRM = 'emailConfirm';

    /**
     * @var bool
     */
    public $enable = false;

    /**
     * @var array
     */
    public $validators;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->enable) {
            // Merge validator configurations with defaults
            foreach ($this->getDefaultValidatorConfigs() as $name => $params) {
                $this->validators[$name] = array_merge(
                    $params,
                    ArrayHelper::getValue($this->validators, $name, [])
                );
            }

            // Sort by priority
            ArrayHelper::multisort($this->validators, 'priority', SORT_DESC);
        }
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($this->enable && $app instanceof WebApplication) {
            $app->on(WebApplication::EVENT_BEFORE_ACTION, function (ActionEvent $event) {
                foreach ($this->validators as $params) {
                    /** @var MultiFactorAuthValidator $validatorClass */
                    $validatorClass = $params['class'];
                    $validatorClass::beforeAction($event, $params);
                }
            });
        }
    }

    /**
     * @param Model|FormModel $model
     * @param string $attribute
     * @param IdentityInterface|null $identity
     * @throws \yii\base\InvalidConfigException
     */
    public function validate($model, $attribute, $identity = null)
    {
        if ($this->enable) {
            // Resolve identity
            $identity = $identity ?: \Yii::$app->user->identity;

            // Iterate validators
            foreach ($this->validators as $params) {
                // Skip on priority is false
                $priority = ArrayHelper::getValue($params, 'priority');
                if ($priority === false) {
                    continue;
                }

                /** @var MultiFactorAuthValidator $validator */
                $validator = \Yii::createObject(array_merge($params, ['identity' => $identity]));

                // Check need validate and run
                if ($validator->beforeValidate($model)) {
                    $validator->validateAttribute($model, $attribute);

                    if ($model->isSecurityRequired()) {
                        $model->addError('', \Yii::t('steroids', 'Требуется дополнительная аутентификация'));
                    }
                    break;
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultValidatorConfigs()
    {
        return [
            self::VALIDATOR_GOOGLE_AUTHENTICATOR => [
                'class' => GoogleAuthenticatorMfaValidator::class,
                'priority' => 10,
            ],
            self::VALIDATOR_RE_CAPTCHA => [
                'class' => ReCaptchaMfaValidator::class,
                'priority' => 5,
            ],
            self::VALIDATOR_GEE_TEST => [
                'class' => GeetestMfaValidator::class,
                'priority' => false,
            ],
            self::VALIDATOR_EMAIL_CONFIRM => [
                'class' => EmailConfirmMfaValidator::class,
                'priority' => 5,
            ],
        ];
    }

}