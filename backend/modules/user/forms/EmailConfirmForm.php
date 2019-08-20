<?php

namespace steroids\modules\user\forms;

use steroids\auth\providers\EmailConfirmationProvider;
use steroids\modules\user\forms\meta\EmailConfirmFormMeta;
use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class EmailConfirmForm extends EmailConfirmFormMeta
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var EmailConfirmationProvider
     */
    public $provider;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['code', function ($attribute) {
                $this->user = $this->provider->check($this->email, $this->$attribute);
                if (!$this->user) {
                    $this->addError($attribute, \Yii::t('steroids', 'Код подтверждения неверен или устарел.'));
                }
            }],
        ]);
    }

    public function confirm()
    {
        if ($this->validate()) {
            return \Yii::$app->user->login($this->user, 3600 * 24 * 30);
        }

        return false;
    }
}
