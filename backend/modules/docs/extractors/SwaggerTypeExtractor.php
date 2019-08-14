<?php

namespace steroids\modules\docs\extractors;

use Doctrine\Common\Annotations\TokenParser;
use steroids\base\BaseSchema;
use steroids\base\Type;
use yii\base\BaseObject;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
     * @var array
     */
    public $refs = [];

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

    public static function fixJson($json)
    {
        $newJSON = '';
        $jsonLength = strlen($json);
        for ($i = 0; $i < $jsonLength; $i++) {
            if ($json[$i] == '"' || $json[$i] == "'") {
                $nextQuote = strpos($json, $json[$i], $i + 1);
                $quoteContent = substr($json, $i + 1, $nextQuote - $i - 1);
                $newJSON .= '"' . str_replace('"', "'", $quoteContent) . '"';
                $i = $nextQuote;
            } else {
                $newJSON .= $json[$i];
            }
        }
        return $newJSON;
    }

    /**
     * @param string $type
     * @param string $inClassName
     * @param string $phpdoc
     * @return array
     * @throws \ReflectionException
     */
    public function extractType($type, $inClassName, $phpdoc = null)
    {
        if (!$type) {
            return [
                'type' => self::DEFAULT_TYPE,
            ];
        }

        $isArray = (bool)preg_match('/\[\]$/', $type);
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

        if ($phpdoc) {
            // Class description
            if (preg_match('/@property(-read)? +[^ ]+ \$[^ ]+ (.*)/u', $phpdoc, $matches)) {
                if (!empty($matches[2])) {
                    $schema['description'] = $matches[2];
                }
            } else {
                // Find first comment line as description
                foreach (explode("\n", $phpdoc) as $line) {
                    $line = preg_replace('/^\s*\\/?\*+/', '', $line);
                    $line = trim($line);
                    if ($line && substr($line, 0, 1) !== '@') {
                        $schema['description'] = $line;
                        break;
                    }
                }

                if (preg_match('/@example (.*)/u', $phpdoc, $matches)) {
                    $schema['example'] = trim($matches[1]);
                }

                // Get description from type param
                if (preg_match('/@(var|type) +([^ |\n]+) (.*)/u', $phpdoc, $matches)) {
                    if (!empty($matches[3])) {
                        $schema['description'] = $matches[3];
                    }
                }
            }
        }

        // Support array/object examples
        if (!empty($schema['example']) && in_array(substr($schema['example'], 0, 1), ['[', '{'])) {
            $schema['example'] = Json::decode(static::fixJson($schema['example']));
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
        // Detect schema
        if (is_subclass_of($className, BaseSchema::class)) {
            return $this->extractSchema($className, $fields);
        }

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
        $phpdoc = null;
        $type = $this->findAttributeType($className, $attribute, $phpdoc);

        return $this->extractType($type, $className, $phpdoc);
    }

    /**
     * @param $className
     * @param null $fields
     * @return array
     * @throws \ReflectionException
     */
    public function extractModelRequest($className, $fields = null)
    {
        /** @var Model|ActiveRecord $model */
        $model = new $className();

        if ($fields === null) {
            $fields = $model->safeAttributes();
        }

        $properties = [];
        foreach ($fields as $attributes) {
            $attributes = explode('.', $attributes);
            $attribute = array_shift($attributes);

            if ($model instanceof BaseActiveRecord && $relation = $model->getRelation($attribute, false)) {
                // Relation
                /** @var Model|ActiveRecord $relationModel */
                $relationModelClass = $relation->modelClass;
                $relationModel = new $relationModelClass();
                $property = $this->extractModelRequest($relationModelClass, array_merge($relationModel->safeAttributes(), $attributes));

                // Check hasMany relation
                if ($relation->multiple) {
                    $property = [
                        'type' => 'array',
                        'items' => $property,
                    ];
                }
            } else {
                $property = $this->extractAttribute($className, $attribute);

                // Steroids meta model
                if (method_exists($className, 'meta')) {
                    $property = array_merge($property, [
                        'description' => $model->getAttributeLabel($attribute),
                        'example' => ArrayHelper::getValue($className::meta(), [$attribute, 'example']),
                    ]);

                    /** @var Type $appType */
                    $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
                    $appType->prepareSwaggerProperty($className, $attribute, $property);
                }
            }

            if ($property) {
                $properties[$attribute] = $property;
            }
        }

        return [
            'type' => 'object',
            'properties' => $properties,
        ];
    }

    /**
     * @param $className
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

        // Detect * => model.*
        foreach ($fields as $key => $name) {
            // Syntax: * => model.*
            if ($key === '*' && preg_match('/\.*$/', $name) !== false) {
                unset($fields[$key]);

                $attribute = substr($name, 0, -2);
                $subClassName = $this->findAttributeType($className, $attribute);
                if ($subClassName) {
                    $subModel = new $subClassName();
                    foreach ($subModel->fields() as $key => $name) {
                        $key = is_int($key) ? $name : $key;
                        $fields[$key] = $attribute . '.' . $name;
                    }
                }
            }
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
            } elseif (is_array($attributes) || (is_string($attributes) && $model instanceof BaseActiveRecord && $model->getRelation($attributes, false))) {
                // Relation
                $relation = $model->getRelation($key);
                $property = $this->extractModel($relation->modelClass, is_array($attributes) ? $attributes : null);

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
                    if (empty($property['description'])) {
                        $property['description'] = $model->getAttributeLabel($attribute);
                    }
                    if (empty($property['example'])) {
                        $property['example'] = ArrayHelper::getValue($modelClass::meta(), [$attribute, 'example']);
                    }

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

    public function extractSchema($className, $fields = null)
    {
        $schemaName = (new \ReflectionClass($className))->getShortName();
        if (!isset($this->refs[$schemaName])) {
            /** @var BaseSchema $schema */
            $schema = new $className();

            if ($fields === null) {
                $fields = $schema->fields();
            }

            $properties = [];
            foreach ($fields as $key => $value) {
                if (is_int($key) && is_string($value)) {
                    $key = $value;
                }

                $property = null;
                $attributes = explode('.', $value);
                $modelClass = $className;
                if (count($attributes) > 1) {
                    $attribute = array_pop($attributes);
                    foreach ($attributes as $item) {
                        $modelClass = $this->findAttributeType($modelClass, $item);
                    }

                    $property = ArrayHelper::getValue($this->extractModel($modelClass, [$attribute]), ['properties', $attribute]);
                } else {
                    $attribute = $value;

                    if ($schema->canGetProperty($attribute, true, false)) {
                        $property = $this->extractAttribute($modelClass, $attribute);
                    } else {
                        $modelClass = $this->findAttributeType($modelClass, 'model');
                        $property = ArrayHelper::getValue($this->extractModel($modelClass, [$attribute]), ['properties', $attribute]);
                    }
                }

                if ($property) {
                    $properties[$key] = $property;
                }
            }

            $this->refs[$schemaName] = [
                'type' => 'object',
                'properties' => $properties,
            ];
        }

        return [
            //'type' => 'schema',
            '$ref' => '#/definitions/' . $schemaName,
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
            return $this->extractType($match[1], $className, $comment);
        }

        return $this->extractType(null, $className, $comment);
    }

    /**
     * @param string $className
     * @param string $attribute
     * @param string $phpdoc
     * @return string|null
     * @throws \ReflectionException
     */
    public function findAttributeType($className, $attribute, &$phpdoc = null)
    {
        $type = '';
        $classInfo = new \ReflectionClass($className);
        $inClassName = $className;

        // Find is class php doc
        if (preg_match('/@property(-read)? +([^ |\n]+) \$' . preg_quote($attribute) . ' .*/u', $classInfo->getDocComment(), $matchClass)) {
            $type = $matchClass[2];
            $phpdoc = $matchClass[0];
        }

        // Find in class property php doc
        if (!$type) {
            $propertyInfo = $classInfo->hasProperty($attribute) ? $classInfo->getProperty($attribute) : null;
            if ($propertyInfo && preg_match('/@(var|type) +([^ |\n]+)/u', $propertyInfo->getDocComment(), $matchProperty)) {
                $type = $matchProperty[2];
                $inClassName = $propertyInfo->getDeclaringClass()->getName();
                $phpdoc = $propertyInfo->getDocComment();
            }
        }

        // Find in getter method
        if (!$type) {
            $getter = 'get' . ucfirst($attribute);
            $methodInfo = $classInfo->hasMethod($getter) ? $classInfo->getMethod($getter) : null;
            if ($methodInfo && preg_match('/@return +([^ |\n]+)/u', $methodInfo->getDocComment(), $matchMethod)) {
                $type = $matchMethod[1];
                $inClassName = $methodInfo->getDeclaringClass()->getName();
                $phpdoc = $methodInfo->getDocComment();
            }
        }

        $type = trim($type);

        if ($type) {
            $singleType = $this->parseSingleType($type);
            if ($singleType) {
                return $singleType;
            }

            return $this->resolveClassName($type, $inClassName);
        }
        return null;
    }

    protected function parseSingleType($type)
    {
        $isArray = preg_match('/\[\]$/', $type);
        $type = preg_replace('/\[\]$/', '', $type);

        // Normalize
        $type = trim($type);
        $type = ArrayHelper::getValue(self::TYPE_ALIASES, $type, $type);

        // Find or return null
        return ArrayHelper::keyExists($type, self::SINGLE_MAPPING)
            ? $type . ($isArray ? '[]' : '')
            : null;
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
        $inClassInfo = new \ReflectionClass($inClassName);
        $inClassNamespace = $inClassInfo->getNamespaceName();
        $tokenParser = new TokenParser(file_get_contents($inClassInfo->getFileName()));
        $useStatements = $tokenParser->parseUseStatements($inClassNamespace);

        $isArray = preg_match('/\[\]$/', $shortName);
        $shortName = preg_replace('/\[\]$/', '', $shortName);
        $className = ArrayHelper::getValue($useStatements, strtolower($shortName), $inClassNamespace . '\\' . $shortName);

        $className = '\\' . ltrim($className, '\\');
        return $className . ($isArray ? '[]' : '');
    }
}
