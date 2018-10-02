<?php

namespace steroids\auth;

use yii\base\BaseObject;

abstract class BaseAuthMethod extends BaseObject
{
    /**
     * @var bool
     */
    public $enable = true;

    abstract public function resolve();
}
