<?php

namespace steroids\types;

use steroids\base\Enum;

class EnumListType extends EnumType
{
    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'DropDownField',
            'attribute' => $attribute,
            'multiple' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return 'varchar(255)[]';
    }

    public function giiRules($metaItem, &$useClasses = [])
    {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;

        //TODO return "['in', 'range' => $className::getKeys()]";

        return [];
    }
}