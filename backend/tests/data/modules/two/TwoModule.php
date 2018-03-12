<?php

namespace tests\data\modules\two;

use steroids\base\Module;

class TwoModule extends Module
{
    public static function siteMap()
    {
        return [
            'admin' => [
                'items' => [
                    'bar' => [
                        'label' => 'Bar',
                    ],
                ]
            ]
        ];
    }
}
