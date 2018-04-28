<?php

namespace steroids\modules\gii\forms;

use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\meta\WidgetEntityMeta;
use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;

class WidgetEntity extends WidgetEntityMeta implements IEntity
{
    /**
     * @return static[]
     */
    public static function findAll()
    {
        $items = [];
        foreach (GiiHelper::findClasses(ClassType::WIDGET) as $item) {
            $items[] = new static($item);
        }

        ArrayHelper::multisort($items, 'name');
        return $items;
    }

    public function save() {
        if ($this->validate()) {
            // TODO

            return true;
        }
        return false;
    }
}
