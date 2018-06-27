<?php

namespace steroids\modules\gii\forms;

/**
 * @property-read bool $isProtected
 */
class FormRelationEntity extends ModelRelationEntity
{
    /**
     * @return ModelEntity|null
     */
    public function getRelationModelEntry()
    {
        return FormEntity::findOne($this->relationModel);
    }
}
