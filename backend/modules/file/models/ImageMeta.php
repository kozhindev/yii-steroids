<?php

namespace steroids\modules\file\models;

use steroids\base\Model;
use steroids\behaviors\TimestampBehavior;
use steroids\modules\file\processors\ImageCrop;
use steroids\modules\file\processors\ImageCropResize;
use steroids\modules\file\processors\ImageResize;
use steroids\modules\file\exceptions\FileException;
use steroids\modules\file\FileModule;

/**
 * @property integer $id
 * @property integer $fileId
 * @property string $folder
 * @property string $fileName
 * @property string $fileMimeType
 * @property boolean $isOriginal
 * @property integer $width
 * @property integer $height
 * @property string $processor
 * @property integer $createTime
 * @property-read string $path
 * @property-read string $url
 */
class ImageMeta extends Model
{
    public static function isImageMimeType($value)
    {
        return in_array($value, [
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png'
        ]);
    }

    public function fields()
    {
        return [
            'id',
            'width',
            'height',
            'url',
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%files_images_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return ltrim($this->folder, '/') . $this->fileName;
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function getPath()
    {
        return FileModule::getInstance()->filesRootPath . $this->getRelativePath();
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function getUrl()
    {
        return FileModule::getInstance()->filesRootUrl . $this->getRelativePath();
    }

    /**
     * @return bool
     * @throws FileException
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Delete file
        if (file_exists($this->getPath()) && !unlink($this->getPath())) {
            throw new FileException('Can not remove image thumb file `' . $this->getRelativePath() . '`.');
        }

        return true;
    }

    /**
     * @param $fileId
     * @return static
     */
    public static function findOriginal($fileId)
    {
        return static::findOne([
            'fileId' => $fileId,
            'isOriginal' => true,
        ]);
    }

    /**
     * @param $fileId
     * @param string [$processorName]
     * @return ImageMeta
     * @throws FileException
     * @throws \yii\base\Exception
     */
    public static function findByProcessor($fileId, $processorName = FileModule::PROCESSOR_NAME_DEFAULT)
    {
        // Check already exists
        /** @var static $imageMeta */
        $imageMeta = static::findOne([
            'fileId' => $fileId,
            'processor' => $processorName,
        ]);
        if ($imageMeta) {
            return $imageMeta;
        }

        $imageMeta = static::cloneOriginal($fileId, $processorName);
        $imageMeta->process($processorName);
        $imageMeta->processor = $processorName;
        $imageMeta->save();

        return $imageMeta;
    }

    /**
     * @param string|array $params
     * @throws FileException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function process($params)
    {
        if (is_string($params)) {
            $processors = FileModule::getInstance()->processors;
            if (!isset($processors[$params])) {
                throw new FileException('Not found processor by name `' . $params . '`');
            }
            $params = $processors[$params];
        }

        /** @var ImageCrop|ImageCropResize|ImageResize $processor */
        $processor = \Yii::createObject($params);
        $processor->filePath = $this->getPath();
        $processor->thumbQuality = FileModule::getInstance()->thumbQuality;
        $processor->run();

        if (isset($params['width']) && isset($params['height'])) {
            $this->width = $processor->width;
            $this->height = $processor->height;
        }
    }

    protected static function cloneOriginal($fileId, $suffix)
    {
        // Get original image
        /** @var static $originalMeta */
        $originalMeta = static::findOriginal($fileId);
        if (!$originalMeta) {
            throw new FileException('Not found original image by id `' . $fileId . '`.');
        }

        // New file meta
        $imageMeta = new static();
        $imageMeta->fileId = $originalMeta->fileId;
        $imageMeta->folder = $originalMeta->folder;
        $imageMeta->fileMimeType = $originalMeta->fileMimeType;

        // Generate new file name
        $extension = pathinfo($originalMeta->fileName, PATHINFO_EXTENSION);
        $thumbFormat = $extension && $extension === 'png' ? 'png' : FileModule::getInstance()->thumbFormat;
        $imageMeta->fileName = pathinfo($originalMeta->fileName, PATHINFO_FILENAME) . '.' . $suffix . '.' . $thumbFormat;

        // Clone original file
        if (!copy($originalMeta->getPath(), $imageMeta->getPath())) {
            throw new FileException('Can not clone original file `' . $originalMeta->getRelativePath() . '` to `' . $imageMeta->getRelativePath() . '`.');
        }

        return $imageMeta;
    }

    /**
     * @param integer $width
     * @param integer $height
     */
    public function checkFixedSize($width, $height)
    {
        if (!$width || !$height) {
            $this->addError('id', \Yii::t('app', 'Fixed height or width must be greater than 0'));
            return;
        }

        if ($this->width < $width && $this->height < $height) {
            $this->addError('id', \Yii::t('app', 'Image is smaller that the given fixed size'));
        }

        if ((int) floor($this->width/$this->height) !== (int) floor($width/$height)) {
            $this->addError('id', \Yii::t('app', 'Image has different height/width ratio than the given size'));
        }
    }

}