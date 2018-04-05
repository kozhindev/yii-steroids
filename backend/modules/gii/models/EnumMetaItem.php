<?php

namespace steroids\modules\gii\models;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\Object;

/**
 * @property-read string $constName
 */
class EnumMetaItem extends Object implements Arrayable
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
    public $value;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $cssClass;

    /**
     * @var array
     */
    public $customColumns = [];

    public function getConstName() {
        return strtoupper($this->name);
    }

    public function fields() {
        $classInfo = new \ReflectionClass($this);
        $fields = [];
        foreach ($classInfo->getProperties() as $property) {
            if ($property->isPublic() && $property->class === static::className() && $property->getName() !== 'metaClass') {
                $fields[] = $property->getName();
            }
        }
        return $fields;
    }

}