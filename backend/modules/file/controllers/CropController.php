<?php

namespace steroids\modules\file\controllers;

use steroids\modules\file\FileModule;
use steroids\modules\file\models\File;
use steroids\modules\file\models\ImageMeta;
use steroids\modules\file\processors\ImageCrop;
use yii\helpers\Json;
use yii\web\Controller;

class CropController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $file = File::findOrPanic(['uid' => \Yii::$app->request->post('uid')]);

        $coordinates = \Yii::$app->request->post('coordinates');
        foreach ($coordinates as $processor => $percents) {
            $image = $file->getImageMeta($processor);
            list($width, $height) = getimagesizefromstring(file_get_contents(ImageMeta::findOriginal($image->fileId)->getPath()));

            $image->process([
                'class' => ImageCrop::class,
                'offsetX' => $width * ($percents['x'] / 100),
                'offsetY' => $height * ($percents['y'] / 100),
                'width' => $width * ($percents['width'] / 100),
                'height' => $height * ($percents['height'] / 100),
            ]);
        }

        return $file->getExtendedAttributes(array_merge([FileModule::PROCESSOR_NAME_DEFAULT], array_keys($coordinates)));
    }
}
