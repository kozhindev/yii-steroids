<?php

namespace tests\data\modules\two\three;

use steroids\base\Module;

class ThreeModule extends Module
{
    public static function siteMap()
    {
        return [
            'admin' => [
                'items' => [
                    'foo' => [
                        'label' => 'FOO !!!',
                    ],
                ]
            ],
            'index' => [
                'label' => 'Index',
            ]
        ];
    }
}
