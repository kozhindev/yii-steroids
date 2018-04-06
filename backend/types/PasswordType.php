<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\gii\models\MetaItem;
use yii\db\Schema;

class PasswordType extends Type
{
    public $min = YII_ENV_DEV ? 1 : 3;
    public $max = 32;

    /**
     * @inheritdoc
     */
    public function getFieldProps($model, $attribute, $item)
    {
        return [
            'component' => 'PasswordField',
            'attribute' => $attribute,
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        return '********';
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING . '(' . $this->max . ')';
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string', 'min' => $this->min, 'max' => $this->max],
        ];
    }
}