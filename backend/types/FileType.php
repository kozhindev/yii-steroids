<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\file\models\File;
use yii\db\Schema;
use yii\helpers\Html;

class FileType extends Type
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'FileField',
            ]
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