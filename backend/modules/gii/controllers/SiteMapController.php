<?php

namespace steroids\modules\gii\controllers;

use steroids\modules\gii\GiiModule;
use yii\web\Controller;

class SiteMapController extends Controller
{
    public static function coreMenuItems()
    {
        return [
            'site-map' => [
                'label' => 'Карта сайта',
                'url' => ['/gii/site-map/index'],
                'urlRule' => 'gii/site-map',
                'order' => 499,
                'accessCheck' => [GiiModule::class, 'accessCheck'],
                'visible' => YII_ENV_DEV,
            ],
        ];
    }

    public function actionIndex()
    {
        $testItem = null;
        $testUrl = \Yii::$app->request->get('url');
        if ($testUrl) {
            $testRequest = clone \Yii::$app->request;
            $testRequest->pathInfo = ltrim($testUrl, '/');
            $parseInfo = \Yii::$app->urlManager->parseRequest($testRequest);
            if ($parseInfo) {
                $testRoute = [$parseInfo[0] ? '/' . $parseInfo[0] : ''] + $parseInfo[1];
                $testItem = \Yii::$app->siteMap->getItem($testRoute);
            }
        }

        return $this->render('index', [
            'items' => \Yii::$app->siteMap->getItems(),
            'testItem' => $testItem,
            'testUrl' => $testUrl,
        ]);
    }

}
