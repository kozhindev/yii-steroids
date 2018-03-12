<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class PrimaryKeyType extends Type
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'HiddenField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_PK;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return false;
    }
}