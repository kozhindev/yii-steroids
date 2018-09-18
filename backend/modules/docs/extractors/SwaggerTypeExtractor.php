<?php

namespace steroids\modules\docs\extractors;

use Doctrine\Common\Annotations\TokenParser;
use steroids\base\Type;
use yii\base\BaseObject;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ControllerDocExtractor
 * @property-read string|null $actionId
 * @property-read string|null $controllerId
 * @property-read string|null $moduleId
 */
class SwaggerTypeExtractor extends BaseObject
{
    const DEFAULT_TYPE = 'string';

    const TYPE_ALIASES = [
        'int' => 'integer',
        'bool' => 'boolean',
        'double' => 'float',
        'true' => 'boolean',
        'false' => 'boolean',
    ];

    const SINGLE_MAPPING = [
        'string' => 'string',
        'integer' => 'number',
        'float' => 'number',
        'boolean' => 'boolean',
        'array' => 'array',
        'resource' => 'object',
        'null' => 'object',
        'callable' => 'object',
        'mixed' => 'object',
        'void' => 'object',
        'object' => 'object',
    ];

    private static $_instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * @param string $type
     * @param string $inClassName
     * @return array
     * @throws \ReflectionException
     */
    public function extractType($type, $inClassName)
    {
        if (!$type) {
            return [
                'type' => self::DEFAULT_TYPE,
            ];
        }

        $isArray = preg_match('/\[\]$/', $type);
        if ($isArray) {
            $type = preg_replace('/\[\]$/', '', $type);
        }

        // Get single type
        $singleType = $this->parseSingleType($type);

        // Check is single type
        if ($singleType) {
            $schema = [
                'type' => ArrayHelper::getValue(self::SINGLE_MAPPING, $singleType) ?: self::DEFAULT_TYPE,
            ];
        } else {
            // Object
            $className = $this->resolveClassName($type, $inClassName);
            $schema = $this->extractObject($className);
        }

        return $isArray
            ? [
                'type' => 'array',
                'items' => $schema,
            ]
            : $schema;
    }

    /**
     * @param string $className
     * @param null $fields
     * @return array
     * @throws \ReflectionException
     */
    public function extractObject($className, $fields = null)
    {
        // Detect model
        if (is_subclass_of($className, Model::class)) {
            return $this->extractModel($className, $fields);
        }

        // Single object
        $properties = [];
        $info = new \ReflectionClass($className);
        foreach ($info->getProperties() as $propertyInfo) {
            if (!$propertyInfo->isPublic()) {
                continue;
            }

            $property = $this->extractAttribute($className, $propertyInfo->name);
            if ($property) {
                $properties[$propertyInfo->name] = $property;
            }
        }

        return [
            'type' => 'object',
            'properties' => $properties,
        ];
    }

    /**
     * @param $className
     * @param $attribute
     * @return array
     * @throws \ReflectionException
     */
    public function extractAttribute($className, $attribute)
    {
        $type = $this->findAttributeType($className, $attribute);

        return $this->extractType($type, $className);
    }

    /**
     * @param string $className
     * @param null $fields
     * @return array
     * @throws \ReflectionException
     */
    public function extractModel($className, $fields = null)
    {
        /** @var Model|ActiveRecord $model */
        $model = new $className();

        if ($fields === null) {
            $fields = $model->fields();
        }

        $properties = [];
        foreach ($fields as $key => $attributes) {
            if (is_int($key) && is_string($attributes)) {
                $key = $attributes;
            }

            $property = null;
            if (is_callable($attributes)) {
                // Method
                $property = $this->extractMethod($className, $attributes);
            } elseif (is_array($attributes)) {
                // Relation
                $relation = $model->getRelation($key);
                $property = $this->extractModel($relation->modelClass, $attributes);

                // Check hasMany relation
                if ($relation->multiple) {
                    $property = [
                        'type' => 'array',
                        'items' => $property,
                    ];
                }
            } elseif (is_string($attributes)) {
                // Single attribute or attributes map
                $attributes = explode('.', $attributes);
                $attribute = array_pop($attributes);

                // Find sub model for attributes map case
                $modelClass = $className;
                if (count($attributes) > 0) {
                    foreach ($attributes as $subAttribute) {
                        $modelClass = $this->findAttributeType($modelClass, $subAttribute);
                    }

                    /** @var \steroids\base\Model $model */
                    $model = new $modelClass();
                }

                $property = $this->extractAttribute($modelClass, $attribute);

                // Steroids meta model
                if (method_exists($modelClass, 'meta')) {
                    $property = array_merge($property, [
                        'description' => $model->getAttributeLabel($attribute),
                        'example' => ArrayHelper::getValue($modelClass::meta(), [$attribute, 'example']),
                    ]);

                    /** @var Type $appType */
                    $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
                    $appType->prepareSwaggerProperty($modelClass, $attribute, $property);
                }
            }

            if ($property) {
                $properties[$key] = $property;
            }
        }

        return [
            'type' => 'object',
            'properties' => $properties,
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return array|null
     * @throws \ReflectionException
     */
    public function extractMethod($className, $methodName)
    {
        $comment = '';

        if (is_array($methodName) && count($methodName) === 2 && is_object($methodName[0]) && is_string($methodName[1])) {
            $info = new \ReflectionClass(get_class($methodName[0]));
            $comment = $info->getMethod($methodName[1])->getDocComment();
        } else {
            // TODO other formats
        }

        if (preg_match('/@return ([a-z0-9_]+)/i', $comment, $match)) {
            return $this->extractType($match[1], $className);
        }

        return $this->extractType(null, $className);
    }

    /**
     * @param string $className
     * @param string $attribute
     * @return string|null
     * @throws \ReflectionException
     */
    public function findAttributeType($className, $attribute)
    {
        $type = null;
        $classInfo = new \ReflectionClass($className);

        // Find is class php doc
        if (preg_match('/@property(-read)? +([^ |\n]+) \$' . preg_quote($attribute) . '/', $classInfo->getDocComment(), $matchClass)) {
            $type = $matchClass[2];
        }

        // Find in class property php doc
        if (!$type) {
            $propertyInfo = $classInfo->hasProperty($attribute) ? $classInfo->getProperty($attribute) : null;
            if ($propertyInfo && preg_match('/@(var|type) +([^ |\n]+)/', $propertyInfo->getDocComment(), $matchProperty)) {
                $type = $matchProperty[2];
            }
        }

        // Find in getter method
        if (!$type) {
            $getter = 'get' . ucfirst($attribute);
            $methodInfo = $classInfo->hasMethod($getter) ? $classInfo->getMethod($getter) : null;
            if ($methodInfo && preg_match('/@return +([^ |\n]+)/', $methodInfo->getDocComment(), $matchMethod)) {
                $type = $matchMethod[1];
            }
        }

        if ($type) {
            $singleType = $this->parseSingleType($type);
            if ($singleType) {
                return $singleType;
            }

            return $this->resolveClassName(trim($type), $className);
        }
        return null;
    }

    protected function parseSingleType($type)
    {
        // Normalize
        $type = trim($type);
        $type = ArrayHelper::getValue(self::TYPE_ALIASES, $type, $type);

        // Find or return null
        return ArrayHelper::keyExists($type, self::SINGLE_MAPPING) ? $type : null;
    }

    /**
     * @param string $shortName
     * @param string $inClassName
     * @return string
     * @throws \ReflectionException
     */
    protected function resolveClassName($shortName, $inClassName)
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

        $isArray = preg_match('/\[\]$/', $shortName);
        $statementKey = preg_replace('/\[\]/', '', strtolower($shortName));
        $className = ArrayHelper::getValue($useStatements, $statementKey, $shortName);
        $className = '\\' . ltrim($className, '\\');
        return $className . ($isArray ? '[]' : '');
    }
}
