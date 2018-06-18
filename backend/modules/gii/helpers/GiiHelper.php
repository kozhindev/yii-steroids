<?php

namespace steroids\modules\gii\helpers;

use steroids\base\Module;
use steroids\helpers\DefaultConfig;
use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\GiiModule;
use steroids\modules\gii\models\ValueExpression;
use yii\db\Schema;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

class GiiHelper
{
    /**
     * @param string $template
     * @param $savePath
     * @param array $params
     * @throws \Throwable
     */
    public static function renderFile($template, $savePath, $params = [])
    {
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir);
        }

        $path = dirname(__DIR__) . '/templates/' . $template . '.php';
        if (file_put_contents($savePath, \Yii::$app->view->renderPhpFile($path, $params)) !== false) {
            $mask = @umask(0);
            @chmod($savePath, 0666);
            @umask($mask);
        }
    }

    /**
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    public static function parseClassName($className)
    {
        $dirs = [];
        foreach (ClassType::getDirData() as $dir) {
            $dirs[] = explode('/', $dir)[0];
        }

        //"steroids\modules\file\models\File"
        $moduleNamespace = [];
        foreach (explode('\\', $className) as $part) {
            if (in_array($part, $dirs)) {
                break;
            }
            $moduleNamespace[] = $part;
        }
        
        $moduleClass = implode('\\', $moduleNamespace) . '\\' . ucfirst(end($moduleNamespace)) . 'Module';
        $moduleId = array_search($moduleClass, static::findModules());

        return [
            'moduleId' => $moduleId,
            'name' => StringHelper::basename($className),
        ];
    }

    public static function getModuleDir($moduleId)
    {
        $module = static::getModuleById($moduleId);
        return dirname((new \ReflectionClass($module))->getFileName());
    }

    /**
     * @param string $moduleId
     * @return Module
     */
    public static function getModuleById($moduleId)
    {
        /** @var Module $module */
        $module = \Yii::$app;
        foreach (explode('.', $moduleId) as $id) {
            $module = $module->getModule($id);
        }
        return $module;
    }

    /**
     * @param $classType
     * @param $moduleId
     * @param $name
     * @return string
     * @throws \ReflectionException
     */
    public static function getClassName($classType, $moduleId, $name)
    {
        $info = new \ReflectionClass(static::getModuleById($moduleId));

        return implode('\\', [
            $info->getNamespaceName(),
            str_replace('*', $name, str_replace('/', '\\', ClassType::getDir($classType))),
            $name
        ]);
    }

    public static function findModules()
    {
        $classes = DefaultConfig::getModuleClasses();
        foreach ($classes as $id => $className) {
            // Skip dev modules
            if ($id === 'debug') {
                unset($classes[$id]);
            }
            if ($id === 'gii' && !GiiModule::getInstance()->showGiiEntries) {
                unset($classes[$id]);
            }
        }
        return $classes;
    }

    public static function findClasses($classType)
    {
        $classes = [];
        foreach (static::findModules() as $moduleId => $className) {
            $moduleDir = dirname((new \ReflectionClass($className))->getFileName());
            $files = FileHelper::findFiles($moduleDir, [
                'only' => [
                    ClassType::getDir($classType) . '/*.php',
                ]
            ]);
            foreach ($files as $file) {
                $name = basename($file, '.php');

                $className = static::getClassName($classType, $moduleId, $name);
                $info = new \ReflectionClass($className);
                if (preg_match('/Meta$/', $info->getParentClass()->name)) {
                    $classes[] = [
                        'moduleId' => $moduleId,
                        'name' => $name,
                    ];
                }
            }
        }
        return $classes;
    }

    public static function getDbTypes()
    {
        $classInfo = new \ReflectionClass(Schema::class);
        return array_values($classInfo->getConstants());
    }

    public static function getTableNames()
    {
        return \Yii::$app->db->schema->tableNames;
    }

    public static function locale($text)
    {
        $text = Html::encode($text);
        $text = new JsExpression('locale.t(\'' . $text . '\')');
        return $text;
    }

    public static function varExport($var, $indent = '', $arrayLine = false)
    {
        $type = gettype($var);
        if (in_array($var, ['true', 'false'])) {
            $type = 'boolean';
        }
        if (is_int($var)) {
            $type = 'int';
        }
        if ($var instanceof ValueExpression) {
            return (string)$var;
        }
        switch ($type) {
            case 'string':
                return "'" . addcslashes($var, "\\\$\'\r\n\t\v\f") . "'";
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                $arrayIndent = !$arrayLine ? "\n" : '';
                foreach ($var as $key => $value) {
                    $r[] = $indent . (!$arrayLine ? '    ' : '')
                        . ($indexed ? '' : static::varExport($key) . ' => ')
                        . static::varExport($value, !$arrayLine ? $indent . '    ' : '', $arrayLine);
                }
                return "[$arrayIndent" . implode(",$arrayIndent", $r) . "$arrayIndent" . $indent . ']';
            case 'boolean':
                return $var ? 'true' : 'false';
            default:
                return var_export($var, TRUE);
        }
    }

    public static function varJsExport($var, $indent = '')
    {
        $code = Json::encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $code = str_replace("\n", "\n$indent", $code);
        $code = str_replace("'", '\u0027', $code);
        $code = str_replace('"', "'", $code);
        $code = str_replace('\u0027', "'", $code);
        $code = str_replace("'true'", "true", $code);
        $code = str_replace("'false'", "false", $code);
        return $code;
    }
}