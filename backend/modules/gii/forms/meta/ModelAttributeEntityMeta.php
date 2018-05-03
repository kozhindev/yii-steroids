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
                'label' => Yii::t('app', 'Attribute'),
                'required' => true
            ],
            'prevName' => [
                'label' => Yii::t('app', 'Previous name')
            ],
            'label' => [
                'label' => Yii::t('app', 'Label')
            ],
            'hint' => [
                'label' => Yii::t('app', 'Hint')
            ],
            'example' => [
                'label' => Yii::t('app', 'Example value')
            ],
            'appType' => [
                'label' => Yii::t('app', 'Type'),
                'required' => true
            ],
            'defaultValue' => [
                'label' => Yii::t('app', 'Default value')
            ],
            'isRequired' => [
                'label' => Yii::t('app', 'Required'),
                'appType' => 'boolean'
            ],
            'isPublishToFrontend' => [
                'label' => Yii::t('app', 'Publish'),
                'appType' => 'boolean'
            ]
        ];
    }
}
