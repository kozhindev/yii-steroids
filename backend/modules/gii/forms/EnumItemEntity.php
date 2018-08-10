<?php

namespace steroids\modules\gii\forms;

use steroids\base\Enum;
use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\meta\EnumItemEntityMeta;
use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;

class EnumItemEntity extends EnumItemEntityMeta
{
    /**
     * @param EnumEntity $entity
     * @return static[]
     * @throws \ReflectionException
     */
    public static function findAll($entity)
    {
        /** @var Enum $enumClass */
        $enumClass = GiiHelper::getClassName(ClassType::ENUM, $entity->moduleId, $entity->name);

        $info = new \ReflectionClass($enumClass);
        $constants = $info->getConstants();

        $customLists = [];
        foreach ($info->getMethods() as $method) {
            if ($method->getNumberOfParameters() === 0 && preg_match('/^get(.+)Data$/', $method->name, $match)) {
                $columnName = lcfirst($match[1]);
                $methodName = $method->name;
                $customLists[$columnName] = $enumClass::$methodName();
            }
        }

        $items = [];
        $cssClasses = $enumClass::getCssClasses();
        foreach ($enumClass::getLabels() as $value => $label) {
            $name = strtolower(array_search($value, $constants));
            $item = new static([
                'name' => $name,
                'value' => $name !== $value ? $value : null,
                'label' => $label,
                'cssClass' => ArrayHelper::getValue($cssClasses, $value),
            ]);

            $custom = [];
            foreach ($customLists as $columnName => $values) {
                $custom[$columnName] = ArrayHelper::getValue($values, $value);
            }
            $item->custom = $custom;

            $items[] = $item;
        }

        return $items;
    }

    public function fields()
    {
        return $this->attributes();
    }

    public function getConstName() {
        return strtoupper($this->name);
    }

    public function renderConstValue() {
        if ($this->value) {
            return is_numeric($this->value) ? $this->value :  "'" . $this->value . "'";
        }
        return "'" . strtolower($this->name) . "'";
    }

}
