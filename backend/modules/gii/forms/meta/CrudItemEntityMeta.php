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
                'label' => Yii::t('app', 'Name'),
            ],
            'showInForm' => [
                'label' => Yii::t('app', 'Show in Form'),
                'appType' => 'boolean'
            ],
            'showInTable' => [
                'label' => Yii::t('app', 'Show in Table'),
                'appType' => 'boolean'
            ],
            'showInView' => [
                'label' => Yii::t('app', 'Show in View'),
                'appType' => 'boolean'
            ]
        ];
    }
}
