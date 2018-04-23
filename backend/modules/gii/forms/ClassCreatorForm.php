<?php

namespace steroids\modules\gii\forms;

use yii\db\ActiveQuery;
use steroids\modules\gii\forms\meta\ClassCreatorFormMeta;

class ClassCreatorForm extends ClassCreatorFormMeta
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
