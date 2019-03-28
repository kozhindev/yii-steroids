<?php

namespace steroids\modules\file\controllers;

use steroids\modules\file\FileModule;
use steroids\modules\file\models\File;
use steroids\modules\file\models\ImageMeta;
use yii\helpers\Json;
use yii\web\Controller;

class UploadController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $mimeTypes = \Yii::$app->request->get('mimeTypes');
        $fixedSize = \Yii::$app->request->get('fixedSize');

        $result = FileModule::getInstance()->upload([
            'mimeTypes' => is_string($mimeTypes) ? explode(',', $mimeTypes) : $mimeTypes,
        ], [
            'fixedSize' => is_string($fixedSize) ? explode(',', $fixedSize) : $fixedSize,
        ]);

        if (isset($result['errors'])) {
            return [
                'error' => implode(', ', $result['errors']),
            ];
        }

        $processor = array_filter(explode(',', (string)\Yii::$app->request->get('imagesProcessor') ?: \Yii::$app->request->get('processor')));

        // Send responses data
        return array_map(
            function ($file) use ($processor) {
                /** @var \steroids\modules\file\models\File $file */
                return $file->getExtendedAttributes($processor);
            },
            $result
        );
    }

    public function actionEditor($CKEditorFuncNum = null)
    {
        $result = FileModule::getInstance()->upload();
        if (!isset($result['errors'])) {
            /** @var File $file */
            $file = $result[0];
            $url = ImageMeta::findByProcessor($file->id, FileModule::PROCESSOR_NAME_ORIGINAL)->url;

            if ($CKEditorFuncNum) {
                return '<script>window.parent.CKEDITOR.tools.callFunction(' . Json::encode($CKEditorFuncNum) . ', ' . Json::encode($url) . ', "");</script>';
            } else {
                $result = [
                    'fileName' => $file->fileName,
                    'uploaded' => 1,
                    'url' => $url,
                ];
                if (\Yii::$app->request->get('uids')) {
                    $result = [$result];
                }
                return Json::encode($result);
            }
        }
    }
}
