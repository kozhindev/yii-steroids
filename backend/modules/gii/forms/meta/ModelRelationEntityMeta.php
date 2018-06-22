<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class ModelRelationEntityMeta extends FormModel
{
    public $type;
    public $name;
    public $relationModel;
    public $relationKey;
    public $selfKey;
    public $viaTable;
    public $viaRelationKey;
    public $viaSelfKey;

    public function rules()
    {
        return [
            [['type', 'name', 'relationModel', 'relationKey', 'selfKey', 'viaTable', 'viaRelationKey', 'viaSelfKey'], 'string', 'max' => 255],
            [['type', 'name', 'relationModel', 'relationKey', 'selfKey'], 'required'],
        ];
    }

    public static function meta()
    {
        return [
            'type' => [
                'label' => Yii::t('steroids', 'Type'),
                'required' => true
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Name'),
                'required' => true
            ],
            'relationModel' => [
                'label' => Yii::t('steroids', 'Model class'),
                'required' => true
            ],
            'relationKey' => [
                'label' => Yii::t('steroids', 'Relation Key'),
                'required' => true
            ],
            'selfKey' => [
                'label' => Yii::t('steroids', 'Self key'),
                'required' => true
            ],
            'viaTable' => [
                'label' => Yii::t('steroids', 'Table name')
            ],
            'viaRelationKey' => [
                'label' => Yii::t('steroids', 'Relation Key')
            ],
            'viaSelfKey' => [
                'label' => Yii::t('steroids', 'Self key')
            ]
        ];
    }
}
