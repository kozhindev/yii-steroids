<?php

namespace steroids\modules\gii\widgets\GiiApplication;

use steroids\base\Widget;
use yii\helpers\Url;

class GiiApplication extends Widget
{
    public function run()
    {
        \Yii::$app->frontendState->set('config.store', [
            'history' => [
                'basename' => Url::to(['/gii/gii/index']),
            ],
        ]);

        return $this->renderReact([
            'siteName' => Url::home(true),
        ]);
    }
}