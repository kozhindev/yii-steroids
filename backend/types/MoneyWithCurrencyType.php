<?php

namespace steroids\types;

use steroids\gii\models\EnumClass;
use steroids\gii\models\MetaItem;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class MoneyWithCurrencyType extends CategorizedStringType
{
    public $currencyEnum;

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'CategorizedStringField',
                'refAttributeOptions' => [
                    self::OPTION_REF_ATTRIBUTE,
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGiiJsMetaItem($metaItem, $item, &$import = [])
    {
        if (!$metaItem->enumClassName) {
            $metaItem->enumClassName = $this->currencyEnum;
        }
        return parent::getGiiJsMetaItem($metaItem, $item, $import);
    }

    /**
     * @inheritdoc
     */
    public function getItems($metaItem) {
        if ($metaItem->refAttribute) {
            return [
                new MetaItem([
                    'metaClass' => $metaItem->metaClass,
                    'name' => $metaItem->refAttribute,
                    'appType' => 'enum',
                    'enumClassName' => $this->currencyEnum,
                    'publishToFrontend' => $metaItem->publishToFrontend,
                ]),
            ];
        }
        return [];
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
            self::OPTION_REF_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'Currency attribute',
                'style' => [
                    'width' => '120px'
                ]
            ],
            self::OPTION_CLASS_NAME => [
                'component' => 'input',
                'label' => 'Enum',
                'list' => ArrayHelper::getColumn(EnumClass::findAll(), 'className'),
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }

}