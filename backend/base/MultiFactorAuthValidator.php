<?php

namespace steroids\base;

use yii\base\ActionEvent;
use yii\validators\Validator;
use yii\web\IdentityInterface;

abstract class MultiFactorAuthValidator extends Validator
{
    /**
     * @var int|bool
     */
    public $priority;

    /**
     * @var IdentityInterface|null
     */
    public $identity;

    /**
     * Handler before action
     * @param ActionEvent $event
     * @param array $params
     */
    public static function beforeAction($event, $params)
    {
    }

    /**
     * Check to create and run validator
     * @param Model|FormModel $model
     * @return bool
     */
    public function beforeValidate($model)
    {
        return false;
    }

    /**
     * @param Model|FormModel $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        return parent::validateAttribute($model, $attribute);
    }
}
