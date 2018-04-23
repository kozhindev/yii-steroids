<?php

namespace steroids\modules\gii\models;

use steroids\base\Enum;

/**
 * @property-read ModuleClass $moduleClass
 * @property-read EnumMetaClass $metaClass
 */
class EnumClass extends BaseClass
{
    private static $_enums;
    private $_metaClass;

    public static function idToClassName($moduleId, $modelName = null)
    {
        if ($modelName !== null) {
            return 'app\\' . str_replace('.', '\\', $moduleId) . '\\enums\\' . ucfirst($modelName);
        } else {
            return parent::idToClassName($moduleId, $modelName);
        }
    }

    /**
     * @return EnumClass[]
     */
    public static function findAll()
    {
        if (self::$_enums === null) {
            self::$_enums = [];

            foreach (self::findFiles('enums') as $path => $className) {
                if (is_subclass_of($className, Enum::className())) {
                    self::$_enums[] = new EnumClass([
                        'className' => $className,
                    ]);
                }
            }
        }
        return self::$_enums;
    }

    /**
     * @param string $className
     * @return EnumClass|null
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
     * @return ModuleClass
     */
    public function getModuleClass()
    {
        $namespace = substr($this->className, 0, strpos($this->className, '\\enums\\'));
        $id = str_replace('\\', '.', preg_replace('/^app\\\\/', '', $namespace));

        return new ModuleClass([
            'className' => self::idToClassName($id),
        ]);
    }

    public function getModuleId()
    {
        return $this->getModuleClass()->id;
    }

    /**
     * @return EnumMetaClass
     */
    public function getMetaClass()
    {
        if ($this->_metaClass === null) {
            $this->_metaClass = new EnumMetaClass([
                'className' => $this->getNamespace() . '\\meta\\' . $this->getName() . 'Meta',
                'enumClass' => $this,
            ]);
        }
        return $this->_metaClass;
    }

    public function fields()
    {
        return [
            'name',
            'className',
            'moduleId',
            'moduleClass',
            'metaClass',
        ];
    }

}