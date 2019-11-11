<?php

namespace steroids\auth\forms;

use steroids\auth\forms\meta\RecoveryEmailPasswordConfirmFormMeta;
use steroids\auth\models\AuthConfirm;
use steroids\validators\PasswordValidator;

class RecoveryEmailPasswordConfirmForm extends RecoveryEmailPasswordConfirmFormMeta
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
            ['newPassword', PasswordValidator::class],
            ['code', function($attribute) {
                $this->confirm = AuthConfirm::findByCode($this->email, $this->code);
                if (!$this->confirm) {
                    $this->addError($attribute, \Yii::t('steroids', 'Код неверен или устарел'));
                }
            }],
            ['newPassword', 'compare', 'compareAttribute' => 'newPasswordAgain'],
        ]);
    }

    public function confirm()
    {
        if ($this->validate()) {
            $this->confirm->markConfirmed();

            $this->confirm->user->passwordHash = \Yii::$app->security->generatePasswordHash($this->newPassword);
            $this->confirm->user->saveOrPanic();

            \Yii::$app->user->login($this->confirm->user);
            $this->accessToken = \Yii::$app->user->accessToken;
        }
    }
}
