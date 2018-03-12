<?php

namespace steroids\types;

use steroids\file\models\File;
use yii\helpers\Html;

class FilesType extends RelationType
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'FileField',
                'multiple' => true,
            ]
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