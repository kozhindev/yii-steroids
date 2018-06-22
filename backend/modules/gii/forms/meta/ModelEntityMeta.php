<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use steroids\modules\gii\forms\ModelAttributeEntity;
use steroids\modules\gii\forms\ModelRelationEntity;
use \Yii;
use yii\db\ActiveQuery;
use steroids\modules\gii\enums\MigrateMode;

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
    public $queryModel;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'tableName', 'queryModel'], 'string', 'max' => 255],
            [['moduleId', 'name'], 'required'],
            ['migrateMode', 'in', 'range' => MigrateMode::getKeys()],
            ['migrateMode', 'default', 'value' => MigrateMode::UPDATE],
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
            'tableName' => [
                'label' => Yii::t('steroids', 'Table name')
            ],
            'migrateMode' => [
                'appType' => 'enum',
                'enumClassName' => MigrateMode::class,
                'label' => Yii::t('steroids', 'Migration mode')
            ],
            'queryModel' => [
                'label' => Yii::t('steroids', 'Query model'),
                'hint' => Yii::t('steroids', 'Set for SearchModel, skip for FormModel')
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
