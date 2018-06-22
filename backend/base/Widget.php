<?php

namespace steroids\base;

use yii\base\Widget as BaseWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

class Widget extends BaseWidget
{
    public $props = [];

    public function renderReact($props = [], $loadScript = true)
    {
        // Add to render queue
        $props = array_merge($props, $this->props);
        $className = get_class($this);
        \Yii::$app->frontendState->add('config.widget.toRender', [
            $this->id,
            $className,
            !empty($props) ? $props : new JsExpression('{}')
        ]);

        // Add to scripts load queue
        if ($loadScript) {
            // Add translations
            $bundleName = $this->getBundleName();
            $langFile = \Yii::getAlias('@webroot/assets/bundle-' . $bundleName . '-lang.json');
            if (file_exists($langFile)) {
                $translations = [];
                foreach (Json::decode(file_get_contents($langFile)) as $translationString) {
                    $translations[$translationString] = \Yii::t('steroids', $translationString);
                }
                \Yii::$app->frontendState->set('config.locale.translations', $translations);
            }

            \Yii::$app->frontendState->add('config.widget.scripts', \Yii::getAlias('@static/assets/bundle-' . $bundleName . '.js'));
            if ($bundleName !== 'steroids') {
                $this->view->registerCssFile(\Yii::getAlias('@static/assets/bundle-' . $bundleName . '.css'), ['position' => View::POS_END]);
            }
        }

        return Html::tag('span', '', ['id' => $this->id]);
    }

    /**
     * Generate bundle name alias extpoint/yii2-frontend npm package
     * @return string
     */
    public function getBundleName() {
        return implode('-', array_filter(array_slice(preg_split('/\\\\/', get_class($this)), 0, -1), function ($name) {
            return preg_match('/[a-z0-9_-]+/', $name) && !in_array($name, ['app', 'modules', 'widgets']);
        }));
    }
}
