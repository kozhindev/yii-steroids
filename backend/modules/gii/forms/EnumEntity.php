<?php

namespace steroids\modules\gii\forms;

use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\meta\EnumEntityMeta;
use steroids\modules\gii\GiiModule;
use steroids\modules\gii\helpers\GiiHelper;
use steroids\modules\gii\models\ValueExpression;
use steroids\modules\gii\traits\EntityTrait;
use yii\helpers\ArrayHelper;

class EnumEntity extends EnumEntityMeta implements IEntity
{
    use EntityTrait;

    /**
     * @return static[]
     * @throws \ReflectionException
     */
    public static function findAll()
    {
        $items = [];
        foreach (GiiHelper::findClasses(ClassType::ENUM) as $item) {
            $className = GiiHelper::getClassName(ClassType::ENUM, $item['moduleId'], $item['name']);
            $items[] = static::findOne($className);
        }

        ArrayHelper::multisort($items, ['moduleId', 'name']);
        return $items;
    }

    public static function findOne($className)
    {
        $entity = new static();
        $entity->attributes = GiiHelper::parseClassName($className);
        $entity->populateRelation('items', EnumItemEntity::findAll($entity));
        return $entity;
    }

    public function fields()
    {
        return array_merge(
            $this->attributes(),
            [
                'customColumns',
                'items',
            ]
        );
    }

    public function save() {
        if ($this->validate()) {
            // Lazy create module
            ModuleEntity::findOrCreate($this->moduleId);

            // Create/update meta information
            GiiHelper::renderFile('enum/meta', $this->getMetaPath(), [
                'enumEntity' => $this,
            ]);
            if (GiiModule::getInstance()->generateJsMeta) {
                GiiHelper::renderFile('enum/meta_js', $this->getMetaJsPath(), [
                    'enumEntity' => $this,
                ]);
            }
            \Yii::$app->session->addFlash('success', 'Meta info enum ' . $this->name . 'Meta updated');

            // Create enum, if not exists
            if (!file_exists($this->getPath())) {
                GiiHelper::renderFile('enum/enum', $this->getPath(), [
                    'enumEntity' => $this,
                ]);
                \Yii::$app->session->addFlash('success', 'Added enum ' . $this->name);
            }

            return true;
        }
        return false;
    }

    public function getClassName()
    {
        return GiiHelper::getClassName(ClassType::ENUM, $this->moduleId, $this->name);
    }

    public function getPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/enums/' . $this->name . '.php';
    }

    public function getMetaPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/enums/meta/' . $this->name . 'Meta.php';
    }

    public function getMetaJsPath()
    {
        return GiiHelper::getModuleDir($this->moduleId) . '/enums/meta/' . $this->name . 'Meta.js';
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderLabels($indent = '')
    {
        $category = strpos('steroids\\', $this->getClassName()) === 0 ? 'steroids' : 'app';
        $labels = [];
        foreach ($this->items as $itemEntity) {
            $labels[] = new ValueExpression(
                'self::' . $itemEntity->getConstName() . ' => Yii::t(\'' . $category . '\', ' . GiiHelper::varExport($itemEntity->label) . ')'
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
        foreach ($this->items as $itemEntity) {
            $lines[] = $indent . '    [this.' . $itemEntity->getConstName() . ']: '
                . '__(' . GiiHelper::varExport($itemEntity->label) . '),';
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
        foreach ($this->items as $itemEntity) {
            if ($itemEntity->cssClass) {
                $cssClasses[] = new ValueExpression('self::' . $itemEntity->getConstName() . ' => ' . GiiHelper::varExport($itemEntity->cssClass));
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
        if (!empty($this->items) && is_array($this->items[0]->custom)) {
            foreach ($this->items[0]->custom as $name => $value) {
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
        foreach ($this->items as $itemEntity) {
            if (isset($itemEntity->custom[$name])) {
                $values[] = new ValueExpression(
                    'self::' . $itemEntity->getConstName() . ' => ' . GiiHelper::varExport($itemEntity->custom[$name]) . ''
                );
            }
        }
        return GiiHelper::varExport($values, $indent);
    }

    /**
     * @param string $indent
     * @return mixed|string
     */
    public function renderJsCssClasses($indent = '')
    {
        $lines = [];
        foreach ($this->items as $itemEntity) {
            if ($itemEntity->cssClass) {
                $lines[] = $indent . '    [this.' . strtoupper($itemEntity->name) . ']: '
                    . '\'' . str_replace("'", "\\'", $itemEntity->cssClass) . '\',';
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
        foreach ($this->items as $itemEntity) {
            if (isset($itemEntity->custom[$name])) {
                $lines[] = $indent . '    [this.' . strtoupper($itemEntity->name) . ']: '
                    . '\'' . str_replace("'", "\\'", $itemEntity->custom[$name]) . '\',';
            }
        }
        return "{\n" . implode("\n", $lines) . "\n" . $indent . '}';
    }

}
