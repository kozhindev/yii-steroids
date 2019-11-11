<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\RegistrationConfirmFormMeta;
use steroids\auth\models\AuthConfirm;

class RegistrationConfirmForm extends RegistrationConfirmFormMeta
{
    /**
     * @var AuthConfirm
     */
    public $confirm;

    /**
     * @var string
     */
    public $accessToken;

    public function fields()
    {
        return [
            'accessToken',
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }],
            ['code', function($attribute) {
                $this->confirm = AuthConfirm::findByCode($this->email, $this->code);
                if (!$this->confirm) {
                    $this->addError($attribute, \Yii::t('steroids', 'Код неверен или устарел'));
                }
            }],
        ]);
    }

    public function confirm()
    {
        if ($this->validate()) {
            $transaction = static::getDb()->beginTransaction();
            try {
                // Confirm
                $this->confirm->markConfirmed();

                // Access token
                \Yii::$app->user->login($this->confirm->user);
                $this->accessToken = \Yii::$app->user->accessToken;

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }
}
