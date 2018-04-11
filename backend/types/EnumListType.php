<?php

namespace steroids\types;

use steroids\base\Enum;

class EnumListType extends EnumType
{
    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'DropDownField',
                'attribute' => $attribute,
                'multiple' => true,
            ],
            $props
        );
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