<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class MoneyType extends Type
{
    const OPTION_CURRENCY = 'currency';

    public $formatter = 'currency';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'NumberField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_DECIMAL . '(19, 4)';
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        return [
            [$attributeEntity->name, 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_CURRENCY,
                'component' => 'InputField',
                'label' => 'Currency',
                //'list' => ['RUB', 'USD', 'EUR', 'BTC', 'XBT', 'YEN', 'JPY', 'GBP'],
            ],
        ];
    }
}
