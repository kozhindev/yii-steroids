<?php

namespace steroids\modules\user\forms;

use Yii;
use steroids\modules\user\forms\meta\PasswordResetChangeFormMeta;
use steroids\modules\user\models\User;
use steroids\modules\user\UserModule;

class PasswordResetChangeForm extends PasswordResetChangeFormMeta
{
    /**
     * @var User
     */
    protected $user;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['token', 'required'],
            ['token', function($attribute) {
                /** @var User $modelClass */
                $modelClass = UserModule::getInstance()->modelsMap['User'];

                $this->user = $modelClass::findOne([
                    'confirmKey' => $this->$attribute,
                ]);
                if (!$this->user) {
                    $this->addError($attribute, Yii::t('steroids', 'Код подтверждения неверен или устарел.'));
                }
            }],
            [['newPassword', 'newPasswordAgain'], 'string', 'min' => YII_ENV_DEV ? 1 : 6],
            ['newPasswordAgain', 'compare',
                'compareAttribute' => 'newPassword',
                'message' => \Yii::t('steroids', 'Пароли должны совпадать'),
            ],
        ]);
    }

    public function reset()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->user->passwordHash = Yii::$app->security->generatePasswordHash($this->newPassword);
        $this->user->confirmKey = null;
        $this->user->saveOrPanic();

        return true;
    }
}
