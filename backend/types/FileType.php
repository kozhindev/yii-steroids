<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\file\models\File;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class FileType extends Type
{
    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'FileField',
                'attribute' => $attribute,
            ],
            $props
        );
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
        if ($model->$attribute) {
            $file = File::findOne($model->$attribute);
            $url = $file ? $file->previewImageUrl : null;
            if (!$url) {
                return '';
            }

            $size = !empty($options['forTable']) ? 22 : 64;

            return Html::img($url, array_merge([
                'width' => $size,
                'height' => $size,
                'alt' => $model->modelLabel,
            ], $options));
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'integer']
        ];
    }
}