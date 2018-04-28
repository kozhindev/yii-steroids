<?php

namespace steroids\types;

use steroids\base\Type;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class AddressType extends Type
{
    const OPTION_ADDRESS_TYPE = 'addressType';
    const OPTION_RELATION_NAME = 'relationName';

    const TYPE_COUNTRY = 'country';
    const TYPE_REGION = 'region';
    const TYPE_CITY = 'city';
    const TYPE_METRO_STATION = 'metroStation';
    const TYPE_ADDRESS = 'address';
    const TYPE_LONGITUDE = 'longitude';
    const TYPE_LATITUDE = 'latitude';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
    {
        $props = array_merge(
            [
                'component' => 'AddressField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @inheritdoc
     */
    public function renderInputWidget($item, $class, $config)
    {
        $config['options']['addressType'] = ArrayHelper::getValue($item, self::OPTION_ADDRESS_TYPE);

        return $class::widget($config);
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        $addressType = ArrayHelper::getValue($attributeEntity, self::OPTION_ADDRESS_TYPE);
        switch ($addressType) {
            case self::TYPE_COUNTRY:
            case self::TYPE_REGION:
            case self::TYPE_CITY:
            case self::TYPE_METRO_STATION:
                return Schema::TYPE_INTEGER;

            case self::TYPE_LONGITUDE:
            case self::TYPE_LATITUDE:
                return Schema::TYPE_DOUBLE;

            default:
            case self::TYPE_ADDRESS:
                return Schema::TYPE_STRING;
        }
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        $addressType = ArrayHelper::getValue($attributeEntity, self::OPTION_ADDRESS_TYPE);
        switch ($addressType) {
            case self::TYPE_COUNTRY:
            case self::TYPE_REGION:
            case self::TYPE_CITY:
            case self::TYPE_METRO_STATION:
                return [
                    [$attributeEntity->name, 'integer'],
                ];

            case self::TYPE_LONGITUDE:
            case self::TYPE_LATITUDE:
                return [
                    [$attributeEntity->name, 'number'],
                ];

            default:
            case self::TYPE_ADDRESS:
                return [
                    [$attributeEntity->name, 'string'],
                ];
        }
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            [
                'attribute' => self::OPTION_ADDRESS_TYPE,
                'component' => 'InputField',
                'label' => 'Address type',
                /*'options' => [
                    self::TYPE_COUNTRY => 'Country',
                    self::TYPE_REGION => 'Region',
                    self::TYPE_CITY => 'City',
                    self::TYPE_METRO_STATION => 'Metro station',
                    self::TYPE_ADDRESS => 'Address (street, house, ..)',
                    self::TYPE_LONGITUDE => 'Longitude',
                    self::TYPE_LATITUDE => 'Latitude',
                ],
                'style' => [
                    'width' => '100px'
                ],*/
            ],
            [
                'attribute' => self::OPTION_RELATION_NAME,
                'component' => 'InputField',
                'label' => 'Relation name',
                /*'list' => 'relations',
                'style' => [
                    'width' => '100px'
                ],*/
            ],
        ];
    }
}