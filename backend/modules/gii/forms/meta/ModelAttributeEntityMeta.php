<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class ModelAttributeEntityMeta extends FormModel
{
    public $name;
    public $prevName;
    public $label;
    public $hint;
    public $example;
    public $appType;
    public $defaultValue;
    public $isRequired;
    public $isPublishToFrontend;

    public function rules()
    {
        return [
            [['name', 'prevName', 'label', 'hint', 'example', 'appType', 'defaultValue'], 'string', 'max' => 255],
            [['name', 'appType'], 'required'],
            [['isRequired', 'isPublishToFrontend'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'name' => [
                'label' => Yii::t('steroids', 'Attribute'),
                'required' => true
            ],
            'prevName' => [
                'label' => Yii::t('steroids', 'Previous name')
            ],
            'label' => [
                'label' => Yii::t('steroids', 'Label')
            ],
            'hint' => [
                'label' => Yii::t('steroids', 'Hint')
            ],
            'example' => [
                'label' => Yii::t('steroids', 'Example value')
            ],
            'appType' => [
                'label' => Yii::t('steroids', 'Type'),
                'required' => true
            ],
            'defaultValue' => [
                'label' => Yii::t('steroids', 'Default value')
            ],
            'isRequired' => [
                'label' => Yii::t('steroids', 'Required'),
                'appType' => 'boolean'
            ],
            'isPublishToFrontend' => [
                'label' => Yii::t('steroids', 'Publish'),
                'appType' => 'boolean'
            ]
        ];
    }
}
