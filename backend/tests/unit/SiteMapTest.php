<?php

namespace tests\unit;

use steroids\base\WebApplication;
use steroids\components\SiteMap;
use steroids\helpers\DefaultConfig;
use yii\helpers\ArrayHelper;

class SiteMapTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadFromModules()
    {
        $appDir = __DIR__ . '/../data/modules';
        $namespace = 'tests\\data\\modules';
        $config = [
            'id' => 'test',
        ];

        new WebApplication(DefaultConfig::getWebConfig($config, $appDir, $namespace));

        /** @var SiteMap $siteMap */
        $siteMap = \Yii::$app->siteMap;
        $items = $siteMap->getItems();

        $this->assertEquals(
            'FOO !!!',
            ArrayHelper::getValue($items, 'admin.items.foo.label')
        );
        $this->assertEquals(
            'Bar',
            ArrayHelper::getValue($items, 'admin.items.bar.label')
        );
        $this->assertEquals(
            'Index',
            ArrayHelper::getValue($items, 'index.label')
        );

        \Yii::$app = null;
    }

    public function testEquals()
    {
        /** @var SiteMap $siteMap */
        $siteMap = new SiteMap();

        $this->assertEquals(true, $siteMap->isUrlEquals('http://google.com', 'http://google.com'));
        $this->assertEquals(false, $siteMap->isUrlEquals(['/qq/ww/ee'], ['/aa/bb/cc']));
        $this->assertEquals(false, $siteMap->isUrlEquals(['/aa/bb/cc', 'foo' => null], ['/aa/bb/cc']));
        $this->assertEquals(true, $siteMap->isUrlEquals(['/aa/bb/cc', 'foo' => null], ['/aa/bb/cc', 'foo' => null]));
        $this->assertEquals(true, $siteMap->isUrlEquals(['/aa/bb/cc', 'foo' => 'qwe'], ['/aa/bb/cc', 'foo' => null]));
        $this->assertEquals(false, $siteMap->isUrlEquals(['/aa/bb/cc', 'foo' => 'qwe'], ['/aa/bb/cc', 'foo' => '555']));
    }

    public function testFind()
    {
        $siteMap = new SiteMap([
            'requestedRoute' => ['/site/index'],
            'items' => $this->getSiteMap(),
        ]);

        $this->assertEquals(['/site/page', 'name' => 'habrahabr'], $siteMap->getItemUrl(['/site/page', 'name' => 'habrahabr']));
        $this->assertEquals(['/site/page', 'name' => 'habrahabr'], $siteMap->getItemUrl('habrahabr.publications'));
        $this->assertEquals([
            ['url' => ['/site/index'], 'items' => null],
            'habrahabr' => ['url' => ['/site/page', 'name' => 'habrahabr'], 'items' => null],
            ['url' => ['/site/page', 'name' => 'geektimes'], 'items' => null],
            ['url' => ['/site/page', 'name' => 'toster'], 'items' => null],
        ], array_map(function($item) {
            return [
                'url' => $item['url'],
                'items' => $item['items']
            ];
        }, $siteMap->getNavItems(null, 1)));
        $this->assertEquals([
            'publications' => ['url' => ['/site/page', 'name' => 'habrahabr', 'category' => 'top'], 'items' => null],
            'hubs' => ['url' => ['/site/hubs'], 'items' => null],
        ], array_map(function($item) {
            return [
                'url' => $item['url'],
                'items' => $item['items']
            ];
        }, $siteMap->getNavItems('habrahabr', 1)));
    }

    protected function getSiteMap() {
        return [
            [
                'label' => 'Главная',
                'url' => ['/site/index'],
                'urlRule' => '/',
            ],
            'habrahabr' => [
                'label' => 'Хабрахабр',
                'redirectToChild' => true,
                'items' => [
                    'publications' => [
                        'label' => 'Публикации',
                        'url' => ['/site/page', 'name' => 'habrahabr'],
                        'urlRule' => '/habrahabr',
                        'redirectToChild' => 'top',
                        'items' => [
                            'all' => [
                                'label' => 'Все подряд',
                                'url' => ['/site/page', 'name' => 'habrahabr', 'category' => 'all'],
                                'urlRule' => '/habrahabr/all',
                            ],
                            'feed' => [
                                'label' => 'По подписке',
                                'url' => ['/site/page', 'name' => 'habrahabr', 'category' => 'feed'],
                                'urlRule' => '/habrahabr/feed',
                            ],
                            'top' => [
                                'label' => 'Лучшие',
                                'url' => ['/site/page', 'name' => 'habrahabr', 'category' => 'top'],
                                'urlRule' => '/habrahabr/top',
                            ],
                            'interesting' => [
                                'label' => 'Интересные',
                                'url' => ['/site/page', 'name' => 'habrahabr', 'category' => 'interesting'],
                                'urlRule' => '/habrahabr/interesting',
                            ],
                        ],
                    ],
                    'hubs' => [
                        'label' => 'Хабы',
                        'url' => ['/site/hubs'],
                        'urlRule' => '/habrahabr/hubs',
                    ],
                ],
            ],
            [
                'label' => 'Geektimes',
                'url' => ['/site/page', 'name' => 'geektimes'],
                'urlRule' => '/geektimes',
            ],
            [
                'label' => 'Тостер',
                'url' => ['/site/page', 'name' => 'toster'],
                'urlRule' => '/toster',
            ],
        ];
    }

}
