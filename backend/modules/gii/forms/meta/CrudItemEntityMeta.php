<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class CrudItemEntityMeta extends FormModel
{
    public $name;
    public $showInForm;
    public $showInTable;
    public $showInView;

    public function rules()
    {
        return [
            ['name', 'string'],
            [['showInForm', 'showInTable', 'showInView'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'name' => [
                'label' => Yii::t('steroids', 'Name'),
            ],
            'showInForm' => [
                'label' => Yii::t('steroids', 'Show in Form'),
                'appType' => 'boolean'
            ],
            'showInTable' => [
                'label' => Yii::t('steroids', 'Show in Table'),
                'appType' => 'boolean'
            ],
            'showInView' => [
                'label' => Yii::t('steroids', 'Show in View'),
                'appType' => 'boolean'
            ]
        ];
    }
}
