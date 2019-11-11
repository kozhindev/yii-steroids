<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\SocialEmailConfirmFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\auth\models\AuthSocial;

class SocialEmailConfirmForm extends SocialEmailConfirmFormMeta
{

    /**
     * @var AuthSocial
     */
    public $social;

    /**
     * @var AuthConfirm
     */
    public $confirm;

    /**
     * @var string
     */
    public $accessToken;

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'accessToken',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
            ['uid', function($attribute) {
                $this->social = AuthSocial::findOne([
                    'uid' => $this->uid,
                    'userId' => null,
                ]);
                if (!$this->social) {
                    $this->addError($attribute, \Yii::t('steroids', 'Код авторизации не найден'));
                }
            }],
            ['code', function($attribute) {
                $this->confirm = AuthConfirm::findByCode($this->email, $this->code);
                if (!$this->confirm) {
                    $this->addError($attribute, \Yii::t('steroids', 'Код неверен или устарел'));
                }
            }],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function confirm()
    {
        if ($this->validate()) {
            $transaction = static::getDb()->beginTransaction();
            try {
                // Confirm
                $this->confirm->markConfirmed();

                // Append user
                $this->social->appendUser($this->email);

                // Login
                \Yii::$app->user->login($this->social->user);
                $this->accessToken = \Yii::$app->user->accessToken;

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }
}
