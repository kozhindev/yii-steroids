<?php

namespace steroids\base;

use yii\filters\Cors;
use yii\web\Controller;

class BaseWebController extends Controller
{
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => \Yii::$app->cors->corsFilterParams,
            ],
        ];
    }
}