<?php

namespace steroids\modules\docs\helpers;

use Doctrine\Common\Annotations\TokenParser;
use yii\helpers\ArrayHelper;

abstract class ExtractorHelper
{
    /**
     * @param string $type
     * @param string $inClassName
     * @return string
     * @throws \ReflectionException
     */
    public static function resolveType($type, $inClassName)
    {
        return !static::isPrimitiveType($type)
            ? static::resolveClassName($type, $inClassName)
            : static::normalizePrimitiveType($type);
    }

    /**
     * @param string $shortName
     * @param string $inClassName
     * @return string
     * @throws \ReflectionException
     */
    public static function resolveClassName($shortName, $inClassName)
    {
        // Check name with namespace
        if (strpos($shortName, '\\') !== false) {
            return $shortName;
        }

        // Fetch use statements
        $controllerInfo = new \ReflectionClass($inClassName);
        $controllerNamespace = $controllerInfo->getNamespaceName();
        $tokenParser = new TokenParser(file_get_contents($controllerInfo->getFileName()));
        $useStatements = $tokenParser->parseUseStatements($controllerNamespace);

        $className = ArrayHelper::getValue($useStatements, strtolower($shortName), $shortName);
        $className = '\\' . ltrim($className, '\\');
        return $className;
    }

    public static function normalizePrimitiveType($string)
    {
        $map = [
            'int' => 'integer',
            'bool' => 'boolean',
            'double' => 'float',
            'true' => 'boolean',
            'false' => 'boolean',
        ];
        return ArrayHelper::getValue($map, $string, $string);
    }

    public static function isPrimitiveType($string)
    {
        $list = [
            'string',
            'integer',
            'float',
            'boolean',
            'array',
            'resource',
            'null',
            'callable',
            'mixed',
            'void',
            'object',
        ];
        return in_array(static::normalizePrimitiveType($string), $list);
    }
}