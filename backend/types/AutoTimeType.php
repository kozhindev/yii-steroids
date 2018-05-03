<?php

namespace steroids\types;

use steroids\behaviors\TimestampBehavior;

class AutoTimeType extends DateTimeType
{
    const OPTION_TOUCH_ON_UPDATE = 'touchOnUpdate';

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($attributeEntity)
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_TOUCH_ON_UPDATE,
                'component' => 'CheckboxField',
                'label' => 'Is update',
            ],
        ];
    }
}