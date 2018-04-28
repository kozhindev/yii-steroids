<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use steroids\modules\gii\forms\EnumItemEntity;
use \Yii;
use yii\db\ActiveQuery;

/**
 * @property-read EnumItemEntity[] $items
 */
abstract class EnumEntityMeta extends FormModel
{
    public $moduleId;
    public $name;
    public $isCustomValues;

    public function rules()
    {
        return [
            [['moduleId', 'name'], 'string', 'max' => 255],
            [['moduleId', 'name'], 'required'],
            ['isCustomValues', 'boolean'],
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
            'isCustomValues' => [
                'label' => Yii::t('app', 'Use custom values'),
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
