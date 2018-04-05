<?php

namespace steroids\modules\gii\models;

use yii\helpers\Inflector;

/**
 * @property-read ModuleClass $moduleClass
 * @property-read ControllerMetaClass $metaClass
 * @property-read string $id
 * @property-read string $routePrefix
 * @property-read string[] $requestFieldsArray
 * @property-read string[] $rolesArray
 */
class ControllerClass extends BaseClass
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $roles;

    private static $_controllers;
    private $_metaClass;

    public static function idToClassName($moduleId, $modelName = null) {
        return 'app\\' . str_replace('.', '\\', $moduleId) . '\\controllers\\' . ucfirst($modelName);
    }

    /**
     * @return ControllerClass[]
     */
    public static function findAll()
    {
        if (self::$_controllers === null) {
            self::$_controllers = [];

            /*foreach (self::findFiles('controllers') as $path => $className) {
                if (is_subclass_of($className, CrudController::className())) {
                    self::$_controllers[] = new ControllerClass([
                        'className' => $className,
                    ]);
                }
            }*/
        }
        return self::$_controllers;
    }

    /**
     * @param string $className
     * @return ControllerClass|null
     */
    public static function findOne($className) {
        foreach (static::findAll() as $modelClass) {
            if ($modelClass->className === $className) {
                return $modelClass;
            }
        }
        return null;
    }

    /**
     * @return ModuleClass
     */
    public function getModuleClass()
    {
        $namespace = substr($this->className, 0, strpos($this->className, '\\controllers\\'));
        $id = str_replace('\\', '.', preg_replace('/^app\\\\/', '', $namespace));

        return new ModuleClass([
            'className' => ModuleClass::idToClassName($id),
        ]);
    }

    /**
     * @return ControllerMetaClass
     */
    public function getMetaClass() {
        if ($this->_metaClass === null) {
            $this->_metaClass = new ControllerMetaClass([
                'className' => $this->getNamespace() . '\\meta\\' . $this->getName() . 'Meta',
                'controllerClass' => $this,
            ]);

            if (class_exists($this->_metaClass->className)) {
                /** @var CrudController $metaClass */
                $metaClass = $this->_metaClass->className;
                $this->_metaClass->setMeta($metaClass::meta());
            }
        }
        return $this->_metaClass;
    }

    public function getId() {
        return preg_replace('/-controller$/', '', Inflector::camel2id($this->name));
    }

    public function getRoutePrefix() {
        $modulePrefix = str_replace('.', '/', $this->moduleClass->id);
        return "/$modulePrefix/{$this->id}";
    }

    public function fields() {
        return [
            'className',
            'name',
            'moduleClass',
            'metaClass',
        ];
    }

}