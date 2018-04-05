<?php

namespace steroids\types;

use steroids\modules\file\models\File;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class FilesType extends RelationType
{
    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'FileField',
            'attribute' => $attribute,
            'multiple' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFieldData($item, $params)
    {
        $initialFiles = [];
        $files = File::findAll(['id' => ArrayHelper::getValue($params, 'fileIds', [])]);
        foreach ($files as $file) {
            $initialFiles[] = [
                'uid' => $file->uid,
                'path' => $file->title,
                'type' => $file->fileMimeType,
                'bytesUploaded' => $file->fileSize,
                'bytesUploadEnd' => $file->fileSize,
                'bytesTotal' => $file->fileSize,
                'resultHttpMessage' => $file->getExtendedAttributes(ArrayHelper::getValue($params, 'processor')),
            ];
        }
        return [
            'initialFiles' => !empty($initialFiles) ? $initialFiles : null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        return implode(' ', array_map(function ($file) use ($model, $options) {
            /** @type File $file */
            $url = $file->previewImageUrl;
            if (!$url) {
                return '';
            }

            $size = !empty($options['forTable']) ? 22 : 64;

            return Html::img($url, array_merge([
                'width' => $size,
                'height' => $size,
                'alt' => $model->modelLabel,
            ], $options));
        }, $model->$attribute));
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'each', 'rule' => ['integer']],
        ];
    }

}