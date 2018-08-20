<?php

namespace steroids\modules\docs\helpers;

use Doctrine\Common\Annotations\TokenParser;
use function PHPSTORM_META\type;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MetaExtractorHelper
{
    protected $models = [];
    protected $paths = [];
    protected $version = '1.0';

    public function getStatic()
    {
        return json_encode(
            [
                'swagger' => '2.0',
                'info' => [
                    'version' => $this->version,
                    'title' => \Yii::$app->name . ' API',
                    'description' => \Yii::$app->name . ' API',
                    'termsOfService' => 'http://swagger.io/terms/',
                    'contact' => [
                        'email' => \Yii::$app->params['adminEmail']
                    ],
                    'license' => [
                        'name' => 'Apache 2.0',
                        'url' => 'http://www.apache.org/licenses/LICENSE-2.0.html'
                    ]
                ],
                'host' => \Yii::$app->request->hostName,
                'basePath' => '/api/' . $this->version,
                'tags' => [
                    $this->tags
                ],
                'schemes' => [
                    'https',
                    'http'
                ],
                'paths' => $this->paths,
                'definitions' => [
                    'meta' => [
                        'type' => 'object'
                    ],
                    'total' => [
                        'type' => 'integer'
                    ],
                    'items' => [
                        'type' => 'object',
                        'properties' => $this->models,
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES);
    }

    protected function setModels(array $docs)
    {
        $this->models = $docs;
    }


    protected function listActions(string $controllerPath, string $controllerId)
    {
        $this->getFieldFromForm($formNameSpase, $methodName);
    }

    protected function getFieldFromForm(string $formNameSpase, string $methodName)
    {
        $formClass = new $formNameSpase;
        $formInfo = new \ReflectionClass($formNameSpase);
        $fields = $formClass->fields();
        if (!$fields) {
            return;
        }
        $modelClassNameSpase = $formClass->createQuery()->modelClass;
        $model = new $modelClassNameSpase;
        $docs = [];
        foreach ($fields as $field) {
            if (!is_string($field)) {       //todo  anonymous function
                continue;
            }
            $fieldMetaDataForm = $this->searcFieldFromModel($formClass, $field);
            if ($fieldMetaData !== null) {
                $docs[$field] = $fieldMetaDataForm;
                continue;
            }
            $fieldMetaDataModel = $this->searcFieldFromModel($model, $field);
            $docs[$field] = $fieldMetaDataModel;
        }
        $this->setModels($docs);
    }

    protected function searcFieldFromModel(Model $model, $field)
    {
        $fieldMetaData = $this->getFromMeta($model, $field);
        if ($fieldMetaData !== null) {
            return $fieldMetaData;
        }
        if ($model->hasMethod('get' . $field)) {
            return $this->getFromRelation($model, $field);
        }
        return null;
    }

    protected function getFromMeta(Model $model, $field)
    {
        $meta = $model::meta();
        if (!array_key_exists($field, $meta)) {
            return null;
        }
        if (array_key_exists('appType', $meta[$field])) {
            $type = TypeConverter::find($meta[$field]['appType']);
            return $type
                ? [
                    'type' => $type
                ]
                : [
                    'type' => 'string',
                ];
        }
        return [
            'type' => 'string',
        ];
    }

    protected function getFromRelation(Model $model, $field)
    {
        $modelInfo = new \ReflectionClass($model);
        $methodDoc = $modelInfo->getMethod('get' . $field)->getDocComment();
        preg_match('/@return ([a-z|]+)/i', $methodDoc, $regularResult);

        if (!$regularResult) {
            return null;
        }

        if ($regularResult[1] === 'ActiveQuery') {
            $relationName = 'get' . $field;
            $relationActiveQuery = $model->$relationName();
            $type = $relationActiveQuery->multiple ? 'object' : 'array';
            return [
                'type' => $type,
                'description' => end(explode('\\', $relationActiveQuery->modelClass)),
                'properties' => $this->getProperties($relationActiveQuery->modelClass, $field),
            ];
        }

        $standartType = TypeConverter::find($regularResult[1]);
        if ($standartType) {
            return [
                'type' => $standartType,
            ];
        }

        $interfaceNamespace = $this->getInterfaceNamespace($model, $regularResult[1]);
        if (!$interfaceNamespace) {
            return null;
        }

        $modelInfo = new \ReflectionClass($interfaceNamespace);
        $propertiesDocs = $modelInfo->getProperties();
        $children = [];
        foreach ($propertiesDocs as $propertyDoc) {
            $methodDoc = $propertyDoc->getDocComment();
            preg_match('/@var ([$a-z|]+) ([$a-z|]+)/i', $methodDoc, $propertyRegularResult);
            if (!count($propertyRegularResult) == 3) {
                continue;
            }
            $standartType = TypeConverter::find($propertyRegularResult[2]);
            if (!$standartType) {
                continue;
            }
            $children[substr($propertyRegularResult[1], 1)] = [
                'type' => $standartType,
            ];
        }
        return [
            'type' => 'object',
            'description' => $regularResult[1],
            'properties' => $children,
        ];
    }

    protected function getInterfaceNamespace(Model $model, string $interface)
    {
        $modelInfo = new \ReflectionClass($model);
        $modelNamespace = $modelInfo->getNamespaceName();
        $modelClassCode = file_get_contents($modelInfo->getFileName());
        $tokenParser = new TokenParser($modelClassCode);
        $useStatements = $tokenParser->parseUseStatements($modelNamespace);
        $InterfaceNamespase = $useStatements[strtolower($interface)];

        if (!$InterfaceNamespase) {
            $traitsNames = $modelInfo->getTraitNames();
            if ($traitsNames) {
                return $this->getNamespaseFromTrait($traitsNames, $interface);
            }
            return null;
        }
        return $InterfaceNamespase;
    }

    protected function getNamespaseFromTrait(array $traitsNames, string $className)
    {
        foreach ($traitsNames as $traitsName) {
            $modelInfo = new \ReflectionClass($traitsName);
            $modelNamespace = $modelInfo->getNamespaceName();
            $modelClassCode = file_get_contents($modelInfo->getFileName());
            $tokenParser = new TokenParser($modelClassCode);
            $useStatements = $tokenParser->parseUseStatements($modelNamespace);
            $formNameSpase = $useStatements[strtolower($className)];
            if ($formNameSpase) {
                return $formNameSpase;
            }
        }
        return null;
    }


    protected function getProperties(string $model, $fields)
    {
        $model = new $model;
        $fields = $model->fields();
        $children = [];
        if ($fields) {
            foreach ($fields as $field) {
                if (!is_string($field)) {       //todo
                    continue;
                }
                $fieldMetaDataModel = $this->searcFieldFromModel($model, $field);
                if ($fieldMetaDataModel !== null) {
                    $children[$field] = $fieldMetaDataModel;
                    continue;
                }
            }
            return $children;
        }
    }
}