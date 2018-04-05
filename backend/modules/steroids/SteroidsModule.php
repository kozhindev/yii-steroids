<?php

namespace steroids\modules\steroids;

use steroids\base\Module;

class SteroidsModule extends Module
{
    public static function siteMap()
    {
        return [
            'steroids-api' => [
                'label' => 'Steroids API Module',
                'visible' => false,
                'accessCheck' => function() {
                    return true;
                },
                'items' => [
                    'fields-fetch' => [
                        'url' => ['/steroids/steroids-fields/fetch'],
                        'urlRule' => 'api/steroids/fields-fetch',
                    ],
                ],
            ],
        ];
    }
}