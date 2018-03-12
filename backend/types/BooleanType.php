<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class BooleanType extends Type
{
    public $formatter = 'boolean';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'CheckboxField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_BOOLEAN;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'boolean'],
        ];
    }
}