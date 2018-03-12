<?php

namespace steroids\types;

use steroids\behaviors\TimestampBehavior;

class AutoTimeType extends DateTimeType
{
    const OPTION_TOUCH_ON_UPDATE = 'touchOnUpdate';

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function giiBehaviors($metaItem)
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_TOUCH_ON_UPDATE => [
                'component' => 'input',
                'label' => 'Is update',
                'type' => 'checkbox',
            ],
        ];
    }
}