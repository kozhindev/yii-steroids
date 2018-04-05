<?php

namespace steroids\modules\gii\models;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\Object;
use yii\helpers\FileHelper;

/**
 * @property-read string $name
 * @property-read string $namespace
 * @property-read string $folderPath
 * @property-read string $filePath
 */
class BaseClass extends Object implements Arrayable
{
    use ArrayableTrait;

    /**
     * @var string
     */
    public $className;

    public static function idToClassName($moduleId, $modelName = null) {
        if ($modelName !== null) {
            return 'app\\' . str_replace('.', '\\', $moduleId) . '\\models\\' . ucfirst($modelName);
        } else {
            $className = implode('', array_map(function($name) { return ucfirst($name); }, explode('.', $moduleId)));
            return 'app\\' . str_replace('.', '\\', $moduleId) . '\\' . $className . 'Module';
        }
    }

    public static function findFiles($dir)
    {
        $modelsClasses = [];
        foreach (\Yii::$app->getModules() as $id => $module) {
            if ($id === 'debug' || $id === 'gii') {
                continue;
            }

            $module = \Yii::$app->getModule($id);
            $classInfo = new \ReflectionClass($module);
            $modulePath = dirname($classInfo->getFileName());
            $moduleNamespace = $classInfo->getNamespaceName();

            $modelFiles = FileHelper::findFiles($modulePath, [
                'only' => [
                    "$dir/*.php",
                    "*/$dir/*.php",
                ]
            ]);
            foreach ($modelFiles as $modelPath) {
                $className = str_replace('.php', '', $modelPath);
                $className = str_replace($modulePath, '', $className);
                $className = str_replace('/', '\\', $className);
                $modelsClasses[$modelPath] = $moduleNamespace . $className;
            }
        }
        return $modelsClasses;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $nameParts = explode('\\', $this->className);
        return array_slice($nameParts, -1)[0];
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return preg_replace('/\\\\[^\\\\]+$/', '', $this->className);
    }

    /**
     * @return string
     */
    public function getFolderPath()
    {
        return \Yii::getAlias('@' . str_replace('\\', '/', $this->getNamespace()));
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->getFolderPath() . '/' . $this->getName() . '.php';
    }

    public function fields() {
        return [
            'className',
            'name',
        ];
    }
}