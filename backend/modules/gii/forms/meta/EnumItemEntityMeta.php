<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class EnumItemEntityMeta extends FormModel
{
    public $name;
    public $value;
    public $label;
    public $cssClass;
    public $custom;

    public function rules()
    {
        return [
            [['name', 'value', 'label', 'cssClass'], 'string', 'max' => 255],
            ['name', 'required'],
            ['custom', 'safe'],
        ];
    }

    public static function meta()
    {
        return [
            'name' => [
                'label' => Yii::t('app', 'Name'),
                'required' => true
            ],
            'value' => [
                'label' => Yii::t('app', 'Value')
            ],
            'label' => [
                'label' => Yii::t('app', 'Label')
            ],
            'cssClass' => [
                'label' => Yii::t('app', 'CSS Class')
            ],
            'custom' => [
                'label' => Yii::t('app', 'Custom values'),
                'appType' => 'custom'
            ]
        ];
    }
}
