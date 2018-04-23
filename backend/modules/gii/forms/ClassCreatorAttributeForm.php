<?php

namespace steroids\modules\gii\forms;

use yii\db\ActiveQuery;
use steroids\modules\gii\forms\meta\ClassCreatorAttributeFormMeta;

class ClassCreatorAttributeForm extends ClassCreatorAttributeFormMeta
{
    public function fields()
    {
        return [
            '*',
        ];
    }

    /**
     * @param ActiveQuery $query
     */
    public function prepare($query)
    {
    }
}
