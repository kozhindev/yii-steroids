<?php

namespace steroids\modules\user\forms;

use steroids\modules\user\forms\meta\EmailConfirmFormMeta;
use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class EmailConfirmForm extends EmailConfirmFormMeta
{
    /**
     * @var User
     */
    public $user;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['code', function ($attribute) {
                /** @var User $modelClass */
                $modelClass = UserModule::getInstance()->modelsMap['User'];

                $this->user = $modelClass::findOne([
                    'email' => $this->email,
                    'confirmKey' => $this->$attribute,
                ]);
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
