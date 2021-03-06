<?php

namespace steroids\modules\file\uploaders;

use steroids\behaviors\UidBehavior;

class PostUploader extends BaseUploader
{
    protected function fetchFiles()
    {
        $postFile = $this->normalizePostFiles(reset($_FILES));

        $files = [];
        foreach ($postFile as $i => $file) {
            $uid = !empty($_GET['uids'][$i]) ? $_GET['uids'][$i] : UidBehavior::generate();
            $files[] = [
                'uid' => $uid,
                'original' => $file,
                'name' => self::generateFileName($file['name'], $uid),
                'title' => $file['name'],
                'bytesUploaded' => $file['size'],
                'bytesTotal' => $file['size'],
                'type' => static::getFileMimeType($file['tmp_name'] ?: $file['type']),
                'path' => null,
            ];
        }
        return $files;
    }

    protected function uploadInternal()
    {
        // Check request max size
        $summaryFilesSize = array_sum(array_map(function ($file) {
            return $file['bytesTotal'];
        }, $this->files));
        if ($summaryFilesSize > $this->maxRequestSize) {
            $this->addError('maxRequestSize', \Yii::t('steroids', 'Summary uploaded files size is too large. Available size: {size} Mb', [
                'size' => round(($this->maxRequestSize / 1024) / 1024),
            ]));
            return false;
        }

        foreach ($this->files as &$file) {
            // Check PHP upload errors
            switch ($file['original']['error']) {
                case UPLOAD_ERR_NO_FILE:
                    $this->addError('files', \Yii::t('steroids', 'Not found file.'));
                    return false;

                case UPLOAD_ERR_PARTIAL:
                    $this->addError('files', \Yii::t('steroids', 'The file was corrupted when downloading. Please try again.'));
                    return false;

                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_INI_SIZE:
                    $this->addError('files', \Yii::t('steroids', 'The downloaded file is too large.'));
                    return false;

                case UPLOAD_ERR_OK:
                    break;

                default:
                    $this->addError('files', \Yii::t('steroids', 'Error loading file. Error code `{code}`.', [
                        'code' => $file['original']['error'],
                    ]));
                    return false;
            }

            // Check file size
            if ($file['bytesTotal'] > $this->maxFileSize) {
                $this->addError('files', \Yii::t('steroids', 'The uploaded file is too large. Max size: {size} Mb', [
                    'size' => round(($this->maxFileSize / 1024) / 1024),
                ]));
                return false;
            }

            $file['path'] = $this->getFilePath($file['name']);

            if (is_array($this->mimeTypes) && !in_array(static::getFileMimeType($file['original']['tmp_name']), $this->mimeTypes)) {
                $this->addError('files', \Yii::t('steroids', 'Incorrect file format.'));
            }

            // Move uploaded file
            if (!copy($file['original']['tmp_name'], $file['path'])) {
                $this->addError('files', \Yii::t('steroids', 'Cannot move uploaded file.'));
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $file
     * @return array
     */
    protected function normalizePostFiles($file)
    {
        if (empty($file)) {
            return [];
        }
        if (!is_array($file['name'])) {
            return [$file];
        }

        $files = [];
        foreach ($file as $key => $values) {
            foreach ($values as $i => $value) {
                if (!isset($files[$i])) {
                    $files[$i] = [];
                }
                $files[$i][$key] = $value;
            }
        }
        return $files;
    }
}
