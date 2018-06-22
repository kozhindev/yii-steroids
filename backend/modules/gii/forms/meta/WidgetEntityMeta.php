<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class WidgetEntityMeta extends FormModel
{
    public $moduleId;
    public $name;
    public $parentName;
    public $withPropTypes;
    public $withConnect;
    public $withGrid;
    public $withForm;
    public $withRouter;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'parentName'], 'string', 'max' => 255],
            [['moduleId', 'name'], 'required'],
            [['withPropTypes', 'withConnect', 'withGrid', 'withForm', 'withRouter'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'moduleId' => [
                'label' => Yii::t('steroids', 'Module ID'),
                'required' => true
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Class name'),
                'required' => true
            ],
            'parentName' => [
                'label' => Yii::t('steroids', 'Parent widget name'),
                'hint' => Yii::t('steroids', 'For create child view')
            ],
            'withPropTypes' => [
                'label' => Yii::t('steroids', 'With PropTypes'),
                'appType' => 'boolean'
            ],
            'withConnect' => [
                'label' => Yii::t('steroids', 'With connect()'),
                'appType' => 'boolean'
            ],
            'withGrid' => [
                'label' => Yii::t('steroids', 'With Grid'),
                'appType' => 'boolean'
            ],
            'withForm' => [
                'label' => Yii::t('steroids', 'With Form'),
                'appType' => 'boolean'
            ],
            'withRouter' => [
                'label' => Yii::t('steroids', 'With Router'),
                'appType' => 'boolean'
            ]
        ];
    }
}
