<?php

namespace steroids\modules\user;

use steroids\base\Module;
use steroids\modules\user\forms\EmailConfirmForm;
use steroids\modules\user\forms\RegistrationEmailForm;
use steroids\modules\user\forms\RegistrationPhoneForm;
use steroids\modules\user\models\User;

class UserModule extends Module
{
    /**
     * @var string|array
     */
    public $loginRedirectUrl;

    /**
     * @var string|array
     */
    public $registrationRedirectUrl;

    /**
     * @var bool
     */
    public $enableCaptcha;

    public $modelsMap;

    public function init()
    {
        parent::init();

        $this->modelsMap = array_merge(
            [
                'User' => User::class,
                'RegistrationEmailForm' => RegistrationEmailForm::class,
                'EmailConfirmForm' => EmailConfirmForm::class,
                'RegistrationPhoneForm' => RegistrationPhoneForm::class,
            ],
            $this->modelsMap ?: []
        );
    }
}
