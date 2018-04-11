<?php

namespace steroids\modules\gii\models;

use steroids\base\Module;

/**
 * @property-read string $id
 */
class ModuleClass extends BaseClass
{
    private static $_modules;

    /**
     * @return ModuleClass[]
     */
    public static function findAll()
    {
        if (self::$_modules === null) {
            self::$_modules = [];

            foreach (\Yii::$app->modules as $id => $module) {
                if (!is_dir(\Yii::getAlias('@app/' . $id))) {
                    continue;
                }

                $module = \Yii::$app->getModule($id);

                /** @type Module $module */
                self::$_modules[] = new static([
                    'className' => $module::className(),
                ]);

                foreach ($module->modules as $subId => $subModule) {
                    $subModule = $module->getModule($subId);

                    /** @type Module $subModule */
                    self::$_modules[] = new static([
                        'className' => $subModule::className(),
                    ]);
                }
            }
        }
        return self::$_modules;
    }

    /**
     * @param string $className
     * @return ModuleClass|null
     */
    public static function findOne($className) {
        if (strpos($className, '\\') === false) {
            $className = self::idToClassName($className);
        }
        foreach (static::findAll() as $moduleClass) {
            if ($moduleClass->className === $className) {
                return $moduleClass;
            }
        }
        return null;
    }

    public function getId() {
        return str_replace('\\', '.', preg_replace('/^app\\\\/', '', $this->namespace));
    }

    public function fields() {
        return [
            'className',
            'name',
            'id',
        ];
    }

}