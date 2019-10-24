<?php

namespace steroids\notifier\providers;

use yii\base\BaseObject;

abstract class BaseProvider extends BaseObject
{
    /**
     * @var array
     */
    public $templates = [];

    public function send($templatePath, $params, $language)
    {
    }
}
