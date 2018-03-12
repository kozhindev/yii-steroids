<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class HtmlType extends Type
{
    public $formatter = 'raw';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'HtmlField',
                'editorConfig' => [
                    'contentsCss' => \Yii::getAlias('@static/assets/bundle-style.js'),
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        // TODO Html validator
        return [
            [$metaItem->name, 'string']
        ];
    }
}