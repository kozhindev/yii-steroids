<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\SearchModel;
use \Yii;

abstract class ClassCreatorAttributeFormMeta extends SearchModel
{
    public $name;
    public $label;
    public $hint;
    public $example;
    public $appType;
    public $defaultValue;
    public $isRequired;
    public $isPublishToFrontend;

    public function rules()
    {
        return [
            [['name', 'label', 'hint', 'example', 'appType', 'defaultValue'], 'string', 'max' => 255],
            [['name', 'appType'], 'required'],
            [['isRequired', 'isPublishToFrontend'], 'boolean'],
        ];
    }

    public static function meta()
    {
        return [
            'name' => [
                'label' => Yii::t('app', 'Атрибут'),
                'required' => true
            ],
            'label' => [
                'label' => Yii::t('app', 'Название')
            ],
            'hint' => [
                'label' => Yii::t('app', 'Подсказка')
            ],
            'example' => [
                'label' => Yii::t('app', 'Пример значения')
            ],
            'appType' => [
                'label' => Yii::t('app', 'Тип'),
                'required' => true
            ],
            'defaultValue' => [
                'label' => Yii::t('app', 'Значение по-умолчанию')
            ],
            'isRequired' => [
                'label' => Yii::t('app', 'Обязательное поле'),
                'appType' => 'boolean'
            ],
            'isPublishToFrontend' => [
                'label' => Yii::t('app', 'Экспортировать по-умолчанию'),
                'appType' => 'boolean'
            ]
        ];
    }
}
