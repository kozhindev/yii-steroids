<?php

namespace steroids\modules\gii\models;

use steroids\base\Model;

/**
 * @property-read ModelMetaClass $metaClass
 * @property-read ModuleClass $moduleClass
 * @property-read string $requestParamName
 */
class ModelClass extends BaseClass
{
    /**
     * @var string
     */
    public $tableName;

    private $_metaClass;

    private static $_models;

    /**
     * @return ModelClass[]
     */
    public static function findAll()
    {
        if (self::$_models === null) {
            self::$_models = [];

            foreach (self::findFiles('models') as $path => $className) {
                /** @type Model $model */
                $info = new \ReflectionClass($className);
                if ($info->isAbstract() || !$info->getConstructor()) {
                    continue;
                }

                $hasRequiredParams = false;
                foreach ($info->getConstructor()->getParameters() as $parameter) {
                    if (!$parameter->isDefaultValueAvailable()) {
                        $hasRequiredParams = true;
                    }
                }
                if ($hasRequiredParams) {
                    continue;
                }

                $model = new $className();
                if ($model instanceof Model) {
                    self::$_models[] = new ModelClass([
                        'className' => $model::className(),
                        'tableName' => $model::tableName(),
                    ]);
                }
            }
        }
        return self::$_models;
    }

    /**
     * @param string $className
     * @return ModelClass|null
     */
    public static function findOne($className)
    {
        foreach (static::findAll() as $modelClass) {
            if ($modelClass->className === $className) {
                return $modelClass;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isFileMetaExists()
    {
        return file_exists($this->getMetaClass()->getFilePath());
    }

    /**
     * @return ModelMetaClass
     */
    public function getMetaClass()
    {
        if ($this->_metaClass === null) {
            $this->_metaClass = new ModelMetaClass([
                'className' => $this->getNamespace() . '\\meta\\' . $this->getName() . 'Meta',
                'modelClass' => $this,
            ]);
        }
        return $this->_metaClass;
    }

    /**
     * @return ModuleClass
     */
    public function getModuleClass()
    {
        $namespace = substr($this->className, 0, strpos($this->className, '\\models\\'));
        $id = str_replace('\\', '.', preg_replace('/^app\\\\/', '', $namespace));

        return new ModuleClass([
            'className' => self::idToClassName($id),
        ]);
    }

    public function getRequestParamName()
    {
        /** @var Model $className */
        $className = $this->className;
        return $className::getRequestParamName();
    }

    public function getCanRules()
    {
        $names = [];
        $info = new \ReflectionClass($this->className);
        foreach ($info->getMethods() as $method) {
            if ($method->isPublic() && $method->getNumberOfRequiredParameters() === 1
                && strpos($method->name, 'can') === 0 && $method->getParameters()[0]->name === 'user') {
                $names[] = lcfirst(substr($method->name, 3));
            }
        }
        return $names;
    }

    public function fields()
    {
        return [
            'className',
            'name',
            'tableName',
            'moduleClass',
            'metaClass',
        ];
    }
}