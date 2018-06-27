<?php

namespace steroids\modules\file\models;

use steroids\base\Model;
use steroids\behaviors\TimestampBehavior;
use steroids\behaviors\UidBehavior;
use steroids\modules\file\exceptions\FileException;
use steroids\modules\file\FileModule;
use steroids\modules\file\structure\Photo;
use yii\helpers\Url;

/**
 * @property integer $id
 * @property string $uid
 * @property string $title
 * @property string $folder
 * @property string $fileName
 * @property string $fileMimeType
 * @property string $fileSize
 * @property integer $createTime
 * @property boolean $isTemp
 * @property-read string $path
 * @property-read string $url
 * @property-read string $downloadUrl
 * @property-read string $downloadName
 */
class File extends Model
{
    public $processors = [
        FileModule::PROCESSOR_NAME_DEFAULT
    ];

    private $_imageMetas = [];

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * @param static|static[] $file
     * @param string|string[] $processors
     * @return array|null
     * @throws \yii\base\Exception
     */
    public static function asPhotos($file, $processors = null)
    {
        $processors = $processors ?: FileModule::getInstance()->defaultProcessors;

        if (is_array($file)) {
            return array_map(function ($model) use ($processors) {
                return static::asPhotos($model, $processors);
            }, $file);
        } elseif ($file) {
            $result = [];
            foreach ((array)$processors as $processor) {
                try {
                    $imageMeta = $file->getImageMeta($processor);
                } catch (FileException $e) {
                    return null;
                }
                $result[$processor] = new Photo($imageMeta->toFrontend([
                    'url',
                    'width',
                    'height',
                ]));
            }
            return $result;
        }
        return null;
    }

    /**
     * @param string $url
     * @return static
     */
    public static function findByUrl($url)
    {
        // Find uid
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}/', $url, $match)) {
            return static::findOne(['uid' => $match[0]]);
        }
        return null;
    }

    /**
     * @param File[]|File $models
     * @param string[] $processors
     * @return File[]|File
     */
    public static function prepareProcessors($models, $processors)
    {
        if (is_array($models)) {
            $models = array_map(function ($model) use ($processors) {
                $model->processors = $processors;
                return $model;
            }, $models);
        } elseif ($models instanceof File) {
            $models->processors = $processors;
        }
        return $models;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            UidBehavior::class,
            TimestampBehavior::class,
        ];
    }

    public function fields()
    {
        return [
            'id',
            'uid',
            'title',
            'folder',
            'fileName',
            'fileMimeType',
            'fileSize' => function(File $model) { // fix call filesize() on toFrontend()
                return $model->fileSize;
            },
            'createTime',
            'url',
            'downloadUrl',
            'images',
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['isTemp', 'boolean'],
            ['title', 'filter', 'filter' => function ($value) {
                return preg_replace('/^[^\\\\\/]*[\\\\\/]/', '', $value);
            }],
            ['title', 'string', 'max' => 255],
            ['folder', 'match', 'pattern' => '/^[a-z0-9+-_\/.]+$/i'],
            ['folder', 'filter', 'filter' => function ($value) {
                return rtrim($value, '/') . '/';
            }],
            ['fileName', 'string'],
            ['fileSize', 'integer'],
            ['fileMimeType', 'default', 'value' => 'text/plain'],
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

    public function getDownloadName()
    {
        $ext = '.' . pathinfo($this->fileName, PATHINFO_EXTENSION);
        return $this->title . (substr($this->title, -4) !== $ext ? $ext : '');
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function getDownloadUrl()
    {
        return Url::to(['/file/download/index', 'uid' => $this->uid, 'name' => $this->getDownloadName()], true);
    }

    /*public function getIconName()
    {
        $ext = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $ext = preg_replace('/[^0-9a-z_]/', '', $ext);
        $iconPath = __DIR__ . '/../../../client/images/fileIcons/' . $ext . '.png';

        return file_exists($iconPath) ? $ext : 'default';
    }*/

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Remove image meta info
        /** @var ImageMeta[] $imagesMeta */
        $imagesMeta = ImageMeta::findAll(['fileId' => $this->id]);
        foreach ($imagesMeta as $imageMeta) {
            if (!$imageMeta->delete()) {
                throw new FileException('Can not remove image meta `' . $imageMeta->getRelativePath() . '` for file `' . $this->id . '`.');
            }
        }

        // Delete file
        if (file_exists($this->getPath()) && !unlink($this->getPath())) {
            throw new FileException('Can not remove file file `' . $this->getRelativePath() . '`.');
        }

        // Check to delete empty folders
        $filesRootPath = FileModule::getInstance()->filesRootPath;
        $folderNames = explode('/', trim($this->folder, '/'));
        foreach ($folderNames as $i => $folderName) {
            $folderPath = implode('/', array_slice($folderNames, 0, count($folderNames) - $i)) . '/';
            $folderAbsolutePath = $filesRootPath . $folderPath;

            // Check dir exists
            if (!file_exists($folderAbsolutePath)) {
                continue;
            }

            // Skip, if dir is not empty
            $handle = opendir($folderAbsolutePath);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    break 2;
                }
            }

            // Remove folder
            if (!rmdir($folderAbsolutePath)) {
                throw new FileException('Can not remove empty folder `' . $folderPath . '`.');
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        // Create ImageMeta for images
        if ($insert && $this->isImage()) {

            // Create instance
            $imageMeta = new ImageMeta([
                'fileId' => $this->id,
                'folder' => $this->folder,
                'fileName' => $this->fileName,
                'fileMimeType' => $this->fileMimeType,
                'isOriginal' => true,
                'processor' => FileModule::PROCESSOR_NAME_ORIGINAL,
            ]);

            // Save
            $imageMeta->process(FileModule::PROCESSOR_NAME_ORIGINAL);
            $imageMeta->saveOrPanic();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param string $processor
     * @return ImageMeta
     */
    public function getImageMeta($processor = FileModule::PROCESSOR_NAME_DEFAULT)
    {
        if (!isset($this->_imageMetas[$processor])) {
            $this->_imageMetas[$processor] = ImageMeta::findByProcessor($this->id, $processor);
        }
        return $this->_imageMetas[$processor];
    }

    public function getImages()
    {
        $images = [];
        if ($this->isImage()) {
            foreach ($this->processors as $processor) {
                $images[$processor] = $this->getImageMeta($processor);
            }
        } elseif (in_array(FileModule::PROCESSOR_NAME_DEFAULT, $this->processors)) {
            $iconsPath = FileModule::getInstance()->iconsRootPath;
            $iconsUrl = FileModule::getInstance()->iconsRootUrl;
            if ($iconsPath && $iconsUrl) {
                $iconName = pathinfo($this->fileName, PATHINFO_EXTENSION) . '.png';
                $images[FileModule::PROCESSOR_NAME_DEFAULT] = [
                    'url' => file_exists($iconsPath . '/' . $iconName)
                        ? $iconsUrl . '/' . $iconName
                        : $iconsUrl . '/txt.png',
                    'width' => 64,
                    'height' => 64,
                ];
            }
        }
        return $images;
    }

    public function getExtendedAttributes($processor = null)
    {
        if ($processor) {
            $this->processors = [$processor];
        }
        return $this->toArray();
    }

    /**
     * Checks if the file's image is of the given size or ratio
     *
     * @param array(integer, integer) $fixedSize
     * @return bool
     */
    public function checkImageFixedSize($fixedSize)
    {
        if (!$this->isImage()) {
            return true;
        }

        $originalImageMeta = $this->getImageMeta(FileModule::PROCESSOR_NAME_ORIGINAL);
        $originalImageMeta->checkFixedSize((int)$fixedSize[0], (int)$fixedSize[1]);

        if ($originalImageMeta->hasErrors()) {
            return false;
        }

        return true;
    }

    public function isImage()
    {
        return ImageMeta::isImageMimeType($this->fileMimeType);
    }
}