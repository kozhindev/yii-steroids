<?php

namespace tests\data\modules\one;

use steroids\base\Module;
use yii\base\BootstrapInterface;

class OneModule extends Module implements BootstrapInterface
{
    public static function siteMap()
    {
        return [
            'admin' => [
                'label' => 'Admin',
                'items' => [
                    'foo' => [
                        'label' => 'Foo',
                    ],
                ]
            ]
        ];
    }

    public function bootstrap($app)
    {
    }
}
