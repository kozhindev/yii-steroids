<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use steroids\modules\gii\forms\EnumItemEntity;
use \Yii;
use yii\db\ActiveQuery;

/**
 * @property-read EnumItemEntity[] $items
 */
abstract class CrudEntityMeta extends FormModel
{
    public $moduleId;
    public $name;
    public $queryModel;
    public $searchModel;
    public $title;
    public $url;
    public $createActionIndex;
    public $withDelete;
    public $withSearch;
    public $createActionCreate;
    public $createActionUpdate;
    public $createActionView;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'queryModel', 'searchModel', 'title', 'url'], 'string', 'max' => 255],
            [['moduleId', 'name', 'queryModel', 'title'], 'required'],
            [['createActionIndex', 'withDelete', 'withSearch', 'createActionCreate', 'createActionUpdate', 'createActionView'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'moduleId' => [
                'label' => Yii::t('app', 'Module ID'),
                'required' => true
            ],
            'name' => [
                'label' => Yii::t('app', 'Class name'),
                'required' => true
            ],
            'queryModel' => [
                'label' => Yii::t('app', 'Query model'),
                'required' => true
            ],
            'searchModel' => [
                'label' => Yii::t('app', 'Search model')
            ],
            'title' => [
                'label' => Yii::t('app', 'Title'),
                'required' => true
            ],
            'url' => [
                'label' => Yii::t('app', 'Url')
            ],
            'createActionIndex' => [
                'label' => Yii::t('app', 'Index action'),
                'appType' => 'boolean'
            ],
            'withDelete' => [
                'label' => Yii::t('app', 'With Delete'),
                'appType' => 'boolean'
            ],
            'withSearch' => [
                'label' => Yii::t('app', 'With Search'),
                'appType' => 'boolean'
            ],
            'createActionCreate' => [
                'label' => Yii::t('app', 'Create action'),
                'appType' => 'boolean'
            ],
            'createActionUpdate' => [
                'label' => Yii::t('app', 'Update action'),
                'appType' => 'boolean'
            ],
            'createActionView' => [
                'label' => Yii::t('app', 'View action'),
                'appType' => 'boolean'
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(EnumItemEntity::class);
    }
}
