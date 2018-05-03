<?php

namespace steroids\modules\gii\forms;

use steroids\base\Model;
use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\meta\ModelRelationEntityMeta;
use steroids\modules\gii\helpers\GiiHelper;
use yii\db\ActiveQuery;

class FormRelationEntity extends ModelRelationEntity
{
    const CLASS_TYPE = ClassType::FORM;
}
