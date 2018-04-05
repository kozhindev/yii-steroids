<?php

namespace steroids\modules\gii\models;

use steroids\base\Enum;
use steroids\modules\gii\helpers\GiiHelper;

/**
 * @property EnumMetaItem[] $meta
 * @property string $jsFilePath
 * @property string[] $customColumns
 */
class EnumMetaClass extends EnumClass
{
    /**
     * @var EnumClass
     */
    public $enumClass;

    /**
     * @var EnumMetaItem[]
     */
    private $_meta;

    /**
     * @return EnumMetaItem[]
     */
    public function getMeta()
    {
        if (!$this->_meta) {
            /** @var Enum $enumClass */
            $enumClass = $this->enumClass->className;

            $this->_meta = [];
            $cssClasses = $enumClass::getCssClasses();

            $info = new \ReflectionClass($enumClass);
            $constants = $info->getConstants();

            $customColumns = [];
            foreach ($info->getMethods() as $method) {
                if ($method->getNumberOfParameters() === 0 && preg_match('/^get(.+)Data$/', $method->name, $match)) {
                    $columnName = lcfirst($match[1]);
                    $methodName = $method->name;
                    $customColumns[$columnName] = $enumClass::$methodName();
                }
            }

            foreach ($enumClass::getLabels() as $value => $label) {
                $item = new EnumMetaItem([
                    'value' => $value,
                    'name' => strtolower(array_search($value, $constants)),
                    'label' => $label,
                    'cssClass' => isset($cssClasses[$value]) ? $cssClasses[$value] : '',
                ]);
                foreach ($customColumns as $columnName => $values) {
                    $item->customColumns[$columnName] = isset($values[$value]) ? $values[$value] : '';
                }
                $this->_meta[] = $item;
            }
        }
        return $this->_meta;
    }

    /**
     * @param EnumMetaItem[] $value
     */
    public function setMeta($value)
    {
        $this->_meta = $value;
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderLabels($indent = '')
    {
        $labels = [];
        foreach ($this->meta as $enumMetaItem) {
            $labels[] = new ValueExpression(
                'self::' . $enumMetaItem->getConstName() . ' => Yii::t(\'app\', ' . GiiHelper::varExport($enumMetaItem->label) . ')'
            );
        }
        return GiiHelper::varExport($labels, $indent);
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderJsLabels($indent = '')
    {
        $lines = [];
        foreach ($this->meta as $enumMetaItem) {
            $lines[] = $indent . '    [this.' . strtoupper($enumMetaItem->name) . ']: '
                . 'locale.t(' . GiiHelper::varExport($enumMetaItem->label) . '),';
        }
        return "{\n" . implode("\n", $lines) . "\n" . $indent . '}';
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderCssClasses($indent = '')
    {
        $cssClasses = [];
        foreach ($this->meta as $enumMetaItem) {
            if ($enumMetaItem->cssClass) {
                $cssClasses[] = new ValueExpression('self::' . $enumMetaItem->getConstName() . ' => ' . GiiHelper::varExport($enumMetaItem->cssClass));
            }
        }
        return !empty($cssClasses) ? GiiHelper::varExport($cssClasses, $indent) : '';
    }

    /**
     * @return string[]
     */
    public function getCustomColumns()
    {
        $columns = [];
        if (!empty($this->meta) && is_array($this->meta[0]->customColumns)) {
            foreach ($this->meta[0]->customColumns as $name => $value) {
                $columns[] = $name;
            }
        }
        return $columns;
    }

    /**
     * @param string $name
     * @param string $indent
     * @return mixed|string
     */
    public function renderCustomColumn($name, $indent = '')
    {
        $values = [];
        foreach ($this->meta as $enumMetaItem) {
            if (isset($enumMetaItem->customColumns[$name])) {
                $values[$enumMetaItem->value] = $enumMetaItem->customColumns[$name];
            }
        }
        return !empty($values) ? GiiHelper::varExport($values, $indent) : '';
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderJsCssClasses($indent = '')
    {
        $lines = [];
        foreach ($this->meta as $enumMetaItem) {
            if ($enumMetaItem->cssClass) {
                $lines[] = $indent . '    [this.' . strtoupper($enumMetaItem->name) . ']: '
                    . '\'' . str_replace("'", "\\'", $enumMetaItem->cssClass) . '\',';
            }
        }
        return !empty($lines) ? "{\n" . implode("\n", $lines) . "\n" . $indent . '}' : '';
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderCustomColumnJs($name, $indent = '')
    {
        $lines = [];
        foreach ($this->meta as $enumMetaItem) {
            if (isset($enumMetaItem->customColumns[$name])) {
                $lines[] = $indent . '    [this.' . strtoupper($enumMetaItem->name) . ']: '
                    . '\'' . str_replace("'", "\\'", $enumMetaItem->customColumns[$name]) . '\',';
            }
        }
        return !empty($lines) ? "{\n" . implode("\n", $lines) . "\n" . $indent . '}' : '';
    }

    public function fields()
    {
        return [
            'className',
            'name',
            'meta',
        ];
    }

    /**
     * @return string
     */
    public function getJsFilePath()
    {
        return $this->getFolderPath() . '/' . $this->getName() . '.js';
    }
}