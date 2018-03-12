<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;

class MoneyType extends Type
{
    const OPTION_CURRENCY = 'currency';

    public $formatter = 'currency';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'MoneyField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_DECIMAL . '(19, 4)';
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_CURRENCY => [
                'component' => 'input',
                'label' => 'Currency',
                'list' => ['RUB', 'USD', 'EUR', 'BTC', 'XBT', 'YEN', 'JPY', 'GBP'],
            ],
        ];
    }
}