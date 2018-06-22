<?php

namespace steroids\modules\file\uploaders;

use steroids\base\FormModel;

class BaseUploader extends FormModel
{
    const EVENT_BEFORE_UPLOAD = 'beforeUpload';
    const EVENT_AFTER_UPLOAD = 'afterUpload';

    public $destinationDir;
    public $maxFileSize = '200M';
    public $maxRequestSize = '200M';
    public $mimeTypes;
    public $files = [];

    public function init()
    {
        $this->files = $this->fetchFiles();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['destinationDir', 'required'],
            ['destinationDir', function () {
                if (!is_writable($this->destinationDir)) {
                    $this->addError('destinationDir', \Yii::t('steroids', 'Destination directory is not writable.'));
                }
            }],
            [['maxFileSize', 'maxRequestSize'], 'filter', 'filter' => function ($value) {
                return min(
                    self::normalizeSize($value),
                    self::normalizeSize(ini_get('upload_max_filesize')),
                    self::normalizeSize(ini_get('post_max_size'))
                );
            }],
            [['maxFileSize', 'maxRequestSize'], 'safe'],
        ];
    }

    /**
     * @param integer|string $size
     * @return integer
     */
    public static function normalizeSize($size)
    {
        $letter = strtoupper(substr($size, -1));
        $size = (int) $size;
        switch ($letter) {
            case 'G':
                $size *= 1024;
            case 'M':
                $size *= 1024;
            case 'K':
                $size *= 1024;
        }
        return (float)$size;
    }

    public static function generateFileName($fileName, $uid)
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        return $uid . ($ext ? '.' . $ext : '');
    }

    public function upload()
    {
        // Create destination directory, if no exists
        if (!file_exists($this->destinationDir)) {
            mkdir($this->destinationDir, 0775, true);
            chmod($this->destinationDir, 0775);
        }

        if (!$this->validate()) {
            return false;
        }

        $this->trigger(self::EVENT_BEFORE_UPLOAD);

        if (!$this->uploadInternal()) {
            return false;
        }

        $this->trigger(self::EVENT_AFTER_UPLOAD);

        return true;
    }

    protected function getFilePath($name)
    {
        return rtrim($this->destinationDir, '/') . '/' . $name;
    }

    protected function fetchFiles()
    {
        return [];
    }

    protected function uploadInternal()
    {
        return true;
    }

    public static function getFileMimeType($path)
    {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($f, $path);
        finfo_close($f);

        return $mimeType;
    }
}
