<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class PasswordType extends Type
{
    public $min = YII_ENV_DEV ? 1 : 3;
    public $max = 255;

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'PasswordField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        return '********';
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_STRING . '(' . $this->max . ')';
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'string', 'min' => $this->min, 'max' => $this->max],
        ];
    }
}