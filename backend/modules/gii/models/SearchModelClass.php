<?php

namespace steroids\modules\gii\models;

use yii\gii\generators\crud\Generator;

/**
 * @property-read ModuleClass $moduleClass
 * @property-read string $routePrefix
 */
class SearchModelClass extends BaseClass
{
    /**
     * @var ModelClass
     */
    public $modelClass;

    /**
     * @return ModuleClass
     */
    public function getModuleClass()
    {
        $namespace = substr($this->className, 0, strpos($this->className, '\\forms\\'));
        $id = str_replace('\\', '.', preg_replace('/^app\\\\/', '', $namespace));

        return new ModuleClass([
            'className' => self::idToClassName($id),
        ]);
    }

    public function renderSearchRules($indent = '') {
        $generator = new Generator();
        $generator->modelClass = $this->modelClass->className;
        return implode(",\n" . $indent, $generator->generateSearchRules());
    }

    public function renderSearchConditions($indent = '') {
        $generator = new Generator();
        $generator->modelClass = $this->modelClass->className;
        return implode("\n" . $indent, $generator->generateSearchConditions());
    }
}