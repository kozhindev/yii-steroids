<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class IntegerType extends Type
{
    public $formatter = 'integer';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'NumberField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'integer'],
        ];
    }

}