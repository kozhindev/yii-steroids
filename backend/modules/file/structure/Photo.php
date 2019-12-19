<?php

namespace steroids\modules\file\structure;

use yii\base\BaseObject;

class Photo extends BaseObject {

    public $url;
    public $width;
    public $height;

    public function fields()
    {
        return [
            'url',
            'width',
            'height',
        ];
    }
}