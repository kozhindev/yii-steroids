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
                'label' => Yii::t('steroids', 'Name'),
                'required' => true
            ],
            'value' => [
                'label' => Yii::t('steroids', 'Value')
            ],
            'label' => [
                'label' => Yii::t('steroids', 'Label')
            ],
            'cssClass' => [
                'label' => Yii::t('steroids', 'CSS Class')
            ],
            'custom' => [
                'label' => Yii::t('steroids', 'Custom values'),
                'appType' => 'custom'
            ]
        ];
    }
}
