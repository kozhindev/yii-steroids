<?php

namespace steroids\modules\file;

use steroids\base\Module;
use steroids\modules\file\models\File;
use steroids\modules\file\models\ImageMeta;
use steroids\modules\file\processors\ImageFitWithCrop;
use steroids\modules\file\processors\ImageResize;
use steroids\modules\file\uploaders\BaseUploader;
use Yii;
use yii\helpers\ArrayHelper;
use frostealth\yii2\aws\s3\Service as AmazonService;
use GuzzleHttp\Psr7;

class FileModule extends Module
{
    const PROCESSOR_NAME_ORIGINAL = 'original';
    const PROCESSOR_NAME_DEFAULT = 'default';
    const PROCESSOR_NAME_THUMBNAIL = 'thumbnail';
    const PROCESSOR_NAME_FULL = 'full';

    const SOURCE_FILE = 'file';
    const SOURCE_AMAZONE_S3 = 'amazone_s3';

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

    public $prioritySource = 'file';

    /**
     * @var AmazonService
     */
    public $amazoneStorage;

    /**
     * Default image processors (used when ImageMeta export)
     * @var array
     */
    public $defaultProcessors = [
        self::PROCESSOR_NAME_THUMBNAIL,
        self::PROCESSOR_NAME_FULL,
    ];

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
                    'crop' => [
                        'url' => ['/file/crop/index'],
                        'urlRule' => 'file/crop',
                    ],
                    'download' => [
                        'url' => ['/file/download/index'],
                        'urlRule' => 'file/<uid:[a-z0-9-]{36}>/<name>',
                    ],
                ]
            ],
        ];
    }

    public function init()
    {
        parent::init();

        // Default processors
        $this->processors = ArrayHelper::merge(
            [
                self::PROCESSOR_NAME_ORIGINAL => [
                    'class' => ImageResize::class,
                    'width' => 1920,
                    'height' => 1200,
                ],
                self::PROCESSOR_NAME_DEFAULT => [
                    'class' => ImageResize::class,
                    'width' => 200,
                    'height' => 200,
                ],
                self::PROCESSOR_NAME_THUMBNAIL => [
                    'class' => ImageFitWithCrop::class,
                    'width' => 400,
                    'height' => 300,
                ],
                self::PROCESSOR_NAME_FULL => [
                    'class' => ImageResize::class,
                    'width' => 1900,
                    'height' => 1200,
                ],
            ],
            $this->processors
        );

        // Create aws s3 service
        if ($this->prioritySource === self::SOURCE_AMAZONE_S3) {
            $this->amazoneStorage = \Yii::createObject(array_merge(
                [
                    'class' => 'frostealth\yii2\aws\s3\Service',
                    'region' => '',
                    'credentials' => [
                        'key' => '',
                        'secret' => '',
                    ],
                    'defaultBucket' => '',
                    'defaultAcl' => 'public-read',
                ],
                $this->amazoneStorage ?: []
            ));
        }

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
    public function upload($uploaderConfig = [], $fileConfig = [], $source = null)
    {
        $source = $source ?: $this->prioritySource;

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
            $file->attributes = ArrayHelper::merge([
                'uid' => $item['uid'],
                'title' => $item['title'],
                'folder' => str_replace([$this->filesRootPath, $item['name']], '', $item['path']),
                'fileName' => $item['name'],
                'fileMimeType' => $item['type'],
                'fileSize' => $item['bytesTotal'],
            ], $fileConfig);

            if (is_readable($file->getPath())) {
                $file->md5 = md5_file($file->getPath());
            }

            if (Yii::$app->user->identity) {
                $file->userId = Yii::$app->user->identity->getId();
            }

            if ($source === self::SOURCE_AMAZONE_S3) {
                $file->sourceType = FileModule::SOURCE_AMAZONE_S3;
                $this->uploadToAmazoneS3($file);
            }

            if (!$file->save()) {
                return [
                    'errors' => $file->getFirstErrors(),
                ];
            }

            if (!empty($fileConfig['fixedSize']) && !$file->checkImageFixedSize($fileConfig['fixedSize'])) {
                return [
                    'errors' => $file->getImageMeta(static::PROCESSOR_NAME_ORIGINAL)->getFirstErrors()
                ];
            }

            if ($source === self::SOURCE_AMAZONE_S3) {
                if ($file->isImage()) {
                    $processors = array_keys(static::getInstance()->processors);

                    // Generate and upload thumb images
                    foreach ($processors as $processor) {
                        $imageMeta = $file->getImageMeta($processor);
                        $this->uploadToAmazoneS3($imageMeta);
                        $imageMeta->saveOrPanic(['amazoneS3Url']);
                    }

                    // Delete local files
                    foreach ($processors as $processor) {
                        unlink($file->getImageMeta($processor)->path);
                    }
                } else {
                    // Delete local files
                    unlink($file->path);
                }
            }

            $files[] = $file;
        }

        return $files;
    }

    /**
     * @param File|ImageMeta $file
     * @param null $sourcePath
     */
    public function uploadToAmazoneS3($file, $sourcePath = null)
    {
        $folder = trim($file->folder, '/');
        $fileName = ($folder ? $folder . '/' : '') . $file->fileName;
        $sourceResource = Psr7\try_fopen($sourcePath ?: $file->path, 'r+');
        $sourceStream = Psr7\stream_for($sourceResource);

        ob_start();
        $this->amazoneStorage
            ->commands()
            ->upload($fileName, $sourceStream)
            ->withContentType($file->fileMimeType)
            ->execute();
        $file->amazoneS3Url = $this->amazoneStorage->getUrl($fileName);
        ob_end_clean();

        $sourceStream->close();
    }
}
