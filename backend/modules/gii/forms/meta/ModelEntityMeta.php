<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use steroids\modules\gii\forms\ModelAttributeEntity;
use steroids\modules\gii\forms\ModelRelationEntity;
use \Yii;
use yii\db\ActiveQuery;

/**
 * @property-read ModelAttributeEntity[] $attributeItems
 * @property-read ModelRelationEntity[] $relationItems
 */
abstract class ModelEntityMeta extends FormModel
{
    public $moduleId;
    public $name;
    public $tableName;
    public $migrateMode;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'tableName'], 'string', 'max' => 255],
            [['moduleId', 'name', 'tableName'], 'required'],
            ['migrateMode', 'boolean'],
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
            'tableName' => [
                'label' => Yii::t('app', 'Table name'),
                'required' => true
            ],
            'migrateMode' => [
                'label' => Yii::t('app', 'Migration mode')
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAttributeItems()
    {
        return $this->hasMany(ModelAttributeEntity::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelationItems()
    {
        return $this->hasMany(ModelRelationEntity::class);
    }
}
