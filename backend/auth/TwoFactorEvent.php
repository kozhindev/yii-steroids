<?php

namespace steroids\auth;

use yii\base\Event;

class TwoFactorEvent extends Event
{
    public $response = [];
}
