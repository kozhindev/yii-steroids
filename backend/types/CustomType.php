<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\gii\helpers\GiiHelper;

class CustomType extends Type
{
    const OPTION_DB_TYPE = 'dbType';

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return $metaItem->dbType;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_DB_TYPE => [
                'component' => 'input',
                'label' => 'Db Type',
                'list' => GiiHelper::getDbTypes(),
            ],
        ];
    }
}