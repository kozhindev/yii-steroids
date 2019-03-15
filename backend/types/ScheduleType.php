<?php

namespace steroids\types;

use steroids\base\Type;

class ScheduleType extends Type
{
    const OPTION_SINCE_TIME_ATTRIBUTE = 'sinceTimeAttribute';
    const OPTION_TILL_TIME_ATTRIBUTE = 'tillTimeAttribute';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'ScheduleField',
                'attribute' => $attribute,
                //'refAttributeOptions' => [
                //    self::OPTION_SINCE_TIME_ATTRIBUTE,
                //    self::OPTION_TILL_TIME_ATTRIBUTE,
                //],
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_SINCE_TIME_ATTRIBUTE,
                'component' => 'InputField',
                'label' => 'Since time attribute',
                /*'list' => 'attributes',
                'style' => [
                    'width' => '80px'
                ]*/
            ],
            [
                'attribute' => self::OPTION_TILL_TIME_ATTRIBUTE,
                'component' => 'InputField',
                'label' => 'Till time attribute',
                /*'list' => 'attributes',
                'style' => [
                    'width' => '80px'
                ]*/
            ],
        ];
    }
}
