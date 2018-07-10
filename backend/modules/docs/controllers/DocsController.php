<?php

namespace steroids\modules\docs\controllers;

use app\core\admin\base\AppAdminModule;
use app\cruises\models\Sailing;
use Doctrine\Common\Annotations\TokenParser;
use Sami\Parser\DocBlockParser;
use Sami\Parser\ParserContext;
use Sami\Sami;
use steroids\modules\docs\helpers\MetaExtractorHelper;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class DocsController extends Controller
{
    public static function siteMap()
    {
        return [
            'doc' => [
                'label' => 'Документация',
                'url' => ['/docs/doc/index'],
                'urlRule' => 'doc',
            ],
        ];
    }


    public function actionIndex()
    {
        $siteMap = \Yii::$app->siteMap->items['api'];
        $items = array_filter(($siteMap->items), function ($item) {
            return $item->visible == true;
        });
        $metaExtractor = new MetaExtractorHelper();
        $metaExtractor->listItems($items);
        $jsonMass = $metaExtractor->getStatic();
        return $jsonMass;
    }
}

