<?php

namespace steroids\modules\gii\forms;

use steroids\modules\gii\enums\ClassType;

class FormAttributeEntity extends ModelAttributeEntity
{
    /**
     * @inheritdoc
     */
    public static function findAll($entity, $classType = ClassType::FORM)
    {
        return parent::findAll($entity, $classType);
    }
}
