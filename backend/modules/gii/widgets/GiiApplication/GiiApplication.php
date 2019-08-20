<?php

namespace steroids\modules\gii\widgets\GiiApplication;

use steroids\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class GiiApplication extends Widget
{
    public function run()
    {
        \Yii::$app->assetManager->bundles = [];

        $debug = 0;
        if ($debug) {
            $this->view->registerJsFile('assets/bundle-common.js');
            $this->view->registerJsFile('assets/bundle-index.js');
            $this->view->registerJsFile('assets/bundle-GiiApplication.js');
            $this->view->registerCssFile('assets/bundle-index.css');
            $this->view->registerCssFile('assets/bundle-GiiApplication.css');
        } else {
            GiiAsset::register($this->view);
        }

        \Yii::$app->frontendState->set('config', [
            'store' => [
                'history' => [
                    'basename' => Url::to(['/gii/gii/index']),
                ],
            ],
            'widget' => [
                'toRender' => [
                    [
                        'w0',
                        'steroids\\modules\\gii\\widgets\\GiiApplication\\GiiApplication',
                        'siteName' => Url::home(true),
                    ]
                ]
            ]
        ]);

        return Html::tag('span', '', ['id' => $this->id]);
    }
}
