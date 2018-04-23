<?php

namespace steroids\modules\gii\enums\meta;

use Yii;
use steroids\base\Enum;

abstract class ClassTypeMeta extends Enum
{
    const MODEL = 'model';
    const FORM = 'form';
    const ENUM = 'enum';

    public static function getLabels()
    {
        return [
            self::MODEL => Yii::t('app', 'Модель'),
            self::FORM => Yii::t('app', 'Форма'),
            self::ENUM => Yii::t('app', 'Перечисление')
        ];
    }
}
