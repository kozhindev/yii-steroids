<?php

namespace steroids\validators;

use yii\validators\StringValidator;

class PasswordValidator extends StringValidator
{
    public function init()
    {
        if ($this->min === null) {
            $this->min = YII_ENV_DEV ? 1 : 6;
        }
        parent::init();
    }
}
