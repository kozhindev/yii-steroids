<?php

namespace steroids\modules\gii\forms\meta;

use steroids\base\FormModel;
use \Yii;

abstract class ModuleEntityMeta extends FormModel
{
    public $id;

    public function rules()
    {
        return [
            ['id', 'string', 'max' => 255],
            ['id', 'required'],
        ];
    }

    public static function meta()
    {
        return [
            'id' => [
                'label' => Yii::t('app', 'Module ID'),
                'required' => true
            ],
        ];
    }
}
