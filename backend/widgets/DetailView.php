<?php

namespace steroids\widgets;

use steroids\components\AuthManager;
use Yii;
use steroids\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class DetailView extends \yii\widgets\DetailView
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var array
     */
    public $controllerMeta;

    protected function renderAttribute($attribute, $index)
    {
        if ($this->model instanceof Model) {
            $authManager = Yii::$app->has('authManager') && Yii::$app->authManager instanceof AuthManager
                ? Yii::$app->authManager
                : null;
            if ($authManager && isset($attribute['attribute'])
                && !$authManager->checkAttributeAccess(Yii::$app->user->model, $this->model, $attribute['attribute'], AuthManager::RULE_MODEL_VIEW)) {
                return '';
            }
        }

        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));
            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $this->renderValue($attribute, $index),
                '{captionOptions}' => $captionOptions,
                '{contentOptions}' =>  $contentOptions,
            ]);
        } else {
            return call_user_func($this->template, $attribute, $index, $this);
        }
    }

    protected function normalizeAttributes()
    {
        if ($this->model instanceof Model && $this->attributes === null) {
            $this->attributes = array_keys(array_filter(ArrayHelper::getValue($this->controllerMeta, 'modelAttributes'), function($item) {
                return !empty($item['showInView']);
            }));
        }
        parent::normalizeAttributes();
    }

    protected function renderValue($attribute, $index) {
        if (isset($attribute['attribute']) && $this->model instanceof Model) {
            $options = ArrayHelper::getValue($attribute, 'options', []);
            return \Yii::$app->types->renderValue($this->model, $attribute['attribute'], $options);
        }

        return $this->formatter->format($attribute['value'], $attribute['format']);
    }
}