<?php

namespace steroids\base;

use Yii;
use yii\base\Widget as BaseWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

class Widget extends BaseWidget
{
    public $props = [];

    public function renderReact($props = [])
    {
        $props = array_merge($props, $this->props);
        $className = get_class($this);
        $scriptUrl = Yii::getAlias('@static/assets/bundle-' . $this->getBundleName() . '.js');

        if (\Yii::$app->has('frontendState')) {
            \Yii::$app->frontendState->add('config.backendWidget.toRender', [$this->id, $className, !empty($props) ? $props : new JsExpression('{}')]);
            \Yii::$app->frontendState->add('config.backendWidget.scripts', $scriptUrl);
        } else {
            $jsArgs = implode(', ', [Json::encode($this->id), Json::encode($className), !empty($props) ? Json::encode($props) : '{}']);
            $this->view->registerJs("__appWidget.render($jsArgs)", View::POS_END, $this->id);
            $this->view->registerJsFile($scriptUrl, ['position' => View::POS_END]);
        }

        return Html::tag('span', '', ['id' => $this->id]);
    }

    /**
     * Generate bundle name alias extpoint/yii2-frontend npm package
     * @return string
     */
    public function getBundleName() {
        return implode('-', array_filter(array_slice(preg_split('/\\\\/', get_class($this)), 0, -1), function ($name) {
            return preg_match('/[a-z0-9_-]+/', $name) && !in_array($name, ['app', 'widgets']);
        }));
    }
}
