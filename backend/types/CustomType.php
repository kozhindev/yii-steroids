<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\gii\helpers\GiiHelper;

class CustomType extends Type
{
    const OPTION_DB_TYPE = 'dbType';

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return $attributeEntity->dbType;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_DB_TYPE,
                'component' => 'DropDownField',
                'label' => 'Db Type',
                'list' => array_map(function($id) {
                    return [
                        'id' => $id,
                        'label' => $id,
                    ];
                }, GiiHelper::getDbTypes()),
            ],
        ];
    }
}