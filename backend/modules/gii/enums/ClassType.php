<?php

namespace steroids\modules\gii\enums;

use steroids\modules\gii\enums\meta\ClassTypeMeta;
use steroids\modules\gii\forms\CrudEntity;
use steroids\modules\gii\forms\EnumEntity;
use steroids\modules\gii\forms\FormEntity;
use steroids\modules\gii\forms\IEntity;
use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\forms\WidgetEntity;

class ClassType extends ClassTypeMeta
{
    /**
     * @param $id
     * @return string|IEntity
     */
    public static function getEntityClass($id)
    {
        $map = [
            self::MODEL => ModelEntity::class,
            self::FORM => FormEntity::class,
            self::ENUM => EnumEntity::class,
            self::CRUD => CrudEntity::class,
            self::WIDGET => WidgetEntity::class,
        ];
        return $map[$id];
    }
}
