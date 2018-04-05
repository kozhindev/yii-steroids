<?php

namespace steroids\components;

use steroids\helpers\DateHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

class FrontendState extends Component
{
    /**
     * @var array
     */
    public $state = [];

    /**
     * @param string|string[] $path
     * @param mixed $value
     */
    public function set($path, $value)
    {
        $pathNames = is_string($path) ? explode('.', $path) : $path;
        $name = array_pop($pathNames);

        $state = &$this->state;
        foreach ($pathNames as $key) {
            if (!isset($state[$key])) {
                $state[$key] = [];
            }
            $state = &$state[$key];
        }

        if (isset($state[$name]) && is_array($state[$name]) && is_array($value)) {
            $state[$name] = array_merge($state[$name], $value);

            if (!ArrayHelper::isAssociative($state[$name])) {
                $state[$name] = array_unique($state[$name]);
            }
        } else {
            $state[$name] = $value;
        }
    }

    /**
     * @param string $path
     * @param mixed $value
     */
    public function add($path, $value)
    {
        $this->set($path, [$value]);
    }

    /**
     * @param View $view
     */
    public function register($view)
    {
        $this->set('config.locale', [
            'language' => \Yii::$app->language,
            'backendTimeZone' => DateHelper::parseTimeZone(\Yii::$app->timeZone),
        ]);

        $view->registerJs('window.APP_REDUX_PRELOAD_STATES = [];', View::POS_HEAD);
        $view->registerJs('window.APP_REDUX_PRELOAD_STATES.push(' . Json::encode($this->state) . ')', View::POS_HEAD);
    }

}