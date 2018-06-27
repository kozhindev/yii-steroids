<?php

namespace steroids\modules\gii\forms;

use steroids\base\FormModel;
use steroids\modules\gii\enums\ClassType;
use yii\helpers\ArrayHelper;

/**
 * @property-read bool $isProtected
 */
class FormAttributeEntity extends ModelAttributeEntity
{
    /**
     * @inheritdoc
     */
    public static function findAll($entity)
    {
        return parent::findAll($entity);
    }

}
