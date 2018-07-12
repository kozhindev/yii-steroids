<?php

namespace steroids\modules\docs\controllers;

use steroids\modules\docs\widgets\SwaggerUi\SwaggerUi;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use steroids\components\SiteMapItem;
use steroids\modules\docs\models\SwaggerJson;
use steroids\modules\docs\extractors\SiteMapDocExtractor;

class DocsController extends Controller
{
    public static function siteMap()
    {
        return [
            'docs' => [
                'label' => 'Документация',
                'url' => ['/docs/docs/index'],
                'urlRule' => 'docs',
                'items' => [
                    'json' => [
                        'visible' => false,
                        'url' => ['/docs/docs/json'],
                        'urlRule' => 'docs/swagger.json',
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->renderContent(SwaggerUi::widget());
    }

    public function actionJson()
    {
        $swaggerJson = new SwaggerJson([
            'siteName' => \Yii::$app->name,
            'hostName' => \Yii::$app->request->hostName,
            'adminEmail' => ArrayHelper::getValue(\Yii::$app->params, 'adminEmail', ''),
        ]);

        $extractor = new SiteMapDocExtractor([
            'items' => array_filter(ArrayHelper::getValue(\Yii::$app->siteMap->getItem('api'), 'items', []), function(SiteMapItem $item) {
                return $item->getVisible();
            }),
            'swaggerJson' => $swaggerJson,
        ]);
        $extractor->run();

        return (string) $swaggerJson;
    }
}

