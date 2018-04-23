<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\SearchModel;
use \Yii;

abstract class ClassCreatorFormMeta extends SearchModel
{
    public $moduleId;
    public $name;
    public $tableName;
    public $classType;

    public function rules()
    {
        return [
            [['moduleId', 'name', 'tableName', 'classType'], 'string', 'max' => 255],
        ];
    }

    public static function meta()
    {
        return [
            'moduleId' => [
                'label' => Yii::t('app', 'ИД Модуля')
            ],
            'name' => [
                'label' => Yii::t('app', 'Имя класса')
            ],
            'tableName' => [
                'label' => Yii::t('app', 'Название таблицы в БД')
            ],
            'classType' => [
                'label' => Yii::t('app', 'Тип класса')
            ]
        ];
    }
}
