<?php

namespace steroids\types;

use steroids\base\Type;

class SizeType extends Type
{
    public $formatter = 'shortSize';

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'number'],
        ];
    }
}