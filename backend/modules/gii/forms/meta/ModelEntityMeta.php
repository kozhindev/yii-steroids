<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use yii\db\ActiveQuery;
use steroids\modules\gii\forms\ModelAttributeEntity;
use steroids\modules\gii\forms\ModelRelationEntity;
use steroids\modules\gii\enums\MigrateMode;
use \Yii;

abstract class ModelEntityMeta extends FormModel
{
    public $moduleId;
    public $name;
    public $tableName;
    public $migrateMode;
    public $queryModel;
    public $className;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'tableName', 'queryModel', 'className'], 'string', 'max' => 255],
            [['moduleId', 'name'], 'required'],
            ['migrateMode', 'in', 'range' => MigrateMode::getKeys()],
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

    public static function meta()
    {
        return [
            'moduleId' => [
                'label' => Yii::t('steroids', 'Module ID'),
                'isRequired' => true
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Class name'),
                'isRequired' => true
            ],
            'tableName' => [
                'label' => Yii::t('steroids', 'Table name')
            ],
            'migrateMode' => [
                'label' => Yii::t('steroids', 'Migration mode'),
                'appType' => 'enum',
                'enumClassName' => MigrateMode::class
            ],
            'queryModel' => [
                'label' => Yii::t('steroids', 'Query model'),
                'hint' => Yii::t('steroids', 'Set for SearchModel, skip for FormModel')
            ],
            'className' => [
                'label' => Yii::t('steroids', 'Class name')
            ]
        ];
    }
}
