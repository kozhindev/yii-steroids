<?php

namespace steroids\modules\gii\models;

use steroids\modules\gii\helpers\GiiHelper;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * @property-read string $dbType
 * @property-read string $parsedDbType
 * @property-read string $phpDocType
 * @property-read MetaItem[] $items
 */
class MetaItem extends BaseObject implements Arrayable
{
    use ArrayableTrait;

    /**
     * @var ModelMetaClass
     */
    public $metaClass;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $oldName;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $hint;

    /**
     * @var string
     */
    public $appType = 'string';

    /**
     * @var bool
     */
    public $required;

    /**
     * @var int|string
     */
    public $defaultValue;

    /**
     * @var bool
     */
    public $publishToFrontend;

    /**
     * @var bool
     */
    public $showInForm;

    /**
     * @var bool
     */
    public $showInFilter;

    /**
     * @var bool
     */
    public $showInTable;

    /**
     * @var bool
     */
    public $showInView;

    /**
     * Property of AutoTimeType
     * @var bool
     */
    public $touchOnUpdate;

    /**
     * Property of MoneyType
     * @var string
     */
    public $currency;

    /**
     * Property of CustomType
     * @var string
     */
    public $dbType;

    /**
     * Property of DateTimeType and DateType
     * @var string
     */
    public $format;

    /**
     * Property of EnumType
     * @var string
     */
    public $enumClassName;

    /**
     * Property of RelationType
     * @var string
     */
    public $relationName;

    /**
     * Property of RelationType
     * @var string
     */
    public $listRelationName;

    /**
     * Property of StringType
     * @var string
     */
    public $stringType;

    /**
     * Property of StringType
     * @var integer
     */
    public $stringLength;

    /**
     * Property of AddressType
     * @var integer
     */
    public $addressType;

    /**
     * @var string
     */
    public $subAppType;

    /**
     * @var string
     */
    public $refAttribute;


}