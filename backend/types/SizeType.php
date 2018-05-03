<?php

namespace steroids\types;

use steroids\base\Type;

class SizeType extends Type
{
    public $formatter = 'shortSize';

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'number'],
        ];
    }
}