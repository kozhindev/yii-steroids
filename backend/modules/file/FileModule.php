<?php

namespace steroids\modules\file;

use steroids\base\Module;
use steroids\modules\file\models\File;
use steroids\modules\file\uploaders\BaseUploader;
use yii\helpers\ArrayHelper;

class FileModule extends Module
{
    const PROCESSOR_NAME_ORIGINAL = 'original';
    const PROCESSOR_NAME_DEFAULT = 'default';

    /**
     * Format is jpg or png
     * @var string
     */
    public $thumbFormat = 'jpg';

    /**
     * From 0 to 100 percents
     * @var string
     */
    public $thumbQuality = 90;

    /**
     * Absolute path to root user files dir
     * @var string
     */
    public $filesRootPath;

    /**
     * Absolute url to root user files dir
     * @var string
     */
    public $filesRootUrl;

    /**
     * Absolute url to file icons directory (if exists)
     * @var string
     */
    public $iconsRootUrl;

    /**
     * Absolute path to file icons directory (if exists)
     * @var string
     */
    public $iconsRootPath;

    /**
     * The name of the x-sendfile header
     * @var string
     */
    public $xHeader = false;

    /**
     * Maximum file size limit
     * @var string
     */
    public $fileMaxSize = '200M';

    /**
     * Image settings
     * @var array
     */
    public $processors = [];

    public static function siteMap()
    {
        return [
            'file' => [
                'label' => 'Модуль загрузки и скачивания файла',
                'visible' => false,
                'accessCheck' => function() {
                    return true;
                },
                'items' => [
                    'upload' => [
                        'url' => ['/file/upload/index'],
                        'urlRule' => 'file/upload',
                    ],
                    'upload-editor' => [
                        'url' => ['/file/upload/editor'],
                        'urlRule' => 'file/upload/editor',
                    ],
                    'download' => [
                        'url' => ['/file/download/index'],
                        'urlRule' => 'file/<uid:[a-z0-9-]{36}>/<name>',
                    ],
                ]
            ]
        ];
    }

    public function init()
    {
        parent::init();

        // Default processors
        $this->processors = ArrayHelper::merge(
            [
                self::PROCESSOR_NAME_ORIGINAL => [
                    'class' => '\steroids\modules\file\processors\ImageResize',
                    'width' => 1920,
                    'height' => 1200,
                ],
                self::PROCESSOR_NAME_DEFAULT => [
                    'class' => '\steroids\modules\file\processors\ImageResize',
                    'width' => 200,
                    'height' => 200,
                ]
            ],
            $this->processors
        );

        // Normalize and set default dir
        if ($this->filesRootPath === null) {
            $this->filesRootPath = \Yii::getAlias('@webroot/assets/');
        } else {
            $this->filesRootPath = rtrim($this->filesRootPath, '/') . '/';
        }
        if ($this->filesRootUrl === null) {
            $this->filesRootUrl = \Yii::getAlias('@web', false) . '/assets/';
        } else {
            $this->filesRootUrl = rtrim($this->filesRootUrl, '/') . '/';
        }

        if ($this->iconsRootUrl) {
            $this->iconsRootUrl = \Yii::getAlias($this->iconsRootUrl);
        }
        if ($this->iconsRootPath) {
            $this->iconsRootPath = \Yii::getAlias($this->iconsRootPath);
        }
    }

    /**
     * @param array $uploaderConfig
     * @param array $fileConfig
     * @return \steroids\modules\file\models\File[]
     * @throws \yii\base\InvalidConfigException
     */
    public function upload($uploaderConfig = [], $fileConfig = [])
    {
        /** @var BaseUploader $uploader */
        $uploader = \Yii::createObject(ArrayHelper::merge([
            'class' => empty($_FILES) ? '\steroids\modules\file\uploaders\PutUploader' : '\steroids\modules\file\uploaders\PostUploader',
            'destinationDir' => $this->filesRootPath,
            'maxFileSize' => $this->fileMaxSize,
        ], $uploaderConfig));

        if (!$uploader->upload()) {
            return [
                'errors' => $uploader->getFirstErrors(),
            ];
        }

        $files = [];
        foreach ($uploader->files as $item) {
            $file = new File();
            $file->attributes = ArrayHelper::merge($fileConfig, [
                'uid' => $item['uid'],
                'title' => $item['title'],
                'folder' => str_replace([$this->filesRootPath, $item['name']], '', $item['path']),
                'fileName' => $item['name'],
                'fileMimeType' => $item['type'],
                'fileSize' => $item['bytesTotal'],
            ]);

            if (!$file->save()) {
                return [
                    'errors' => $file->getFirstErrors(),
                ];
            }

            if (!empty($fileConfig['fixedSize']) && !$file->checkImageFixedSize($fileConfig['fixedSize'])) {
                return [
                    'errors' => $file->getImageMeta(FileModule::PROCESSOR_NAME_ORIGINAL)->getFirstErrors()
                ];
            }
            $files[] = $file;
        }

        return $files;
    }
}