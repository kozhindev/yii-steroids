<?php

namespace steroids\modules\docs\helpers;

use app\api\base\BaseApiController;
use app\api\forms\SailingsForm;
use Doctrine\Common\Annotations\TokenParser;
use function PHPSTORM_META\type;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MetaExtractorHelper
{
    protected $controllers = [];
    protected $models = [];
    protected $tags = [];
    protected $paths = [];
    protected $version = '1.0';

    public function getStatic()
    {
        return json_encode(
            [
                'swagger' => '2.0',
                'info' => [
                    'description' => 'Golden Cruises API',
                    'version' => '1.0.0',
                    'title' => 'Golden Cruises API',
                    'termsOfService' => 'http://swagger.io/terms/',
                    'contact' => [
                        'email' => 'example@gmail.com'
                    ],
                    'license' => [
                        'name' => 'Apache 2.0',
                        'url' => 'http://www.apache.org/licenses/LICENSE-2.0.html'
                    ]
                ],
                'host' => 'goldencruises.ru',
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

    public function listItems($items)
    {
        foreach ($items as $item) {
            if ($item->url) {
                $this->listActions($item->url[0], $item->id);
                continue;
            }
            if (!empty($item->items)) {
                $this->listItems($item->items);
            }
        }
    }

    protected function listActions(string $controllerPath, string $controllerId)
    {
        $contoller = \Yii::$app->createController($controllerPath)[0];
        $controllerInfo = new \ReflectionClass($contoller);
        $controllerNamespace = $controllerInfo->getNamespaceName();
        $controllerClassCode = file_get_contents($controllerInfo->getFileName());
        $tokenParser = new TokenParser($controllerClassCode);
        $useStatements = $tokenParser->parseUseStatements($controllerNamespace);

        $actions = array_filter($controllerInfo->getMethods(\ReflectionMethod::IS_PUBLIC), function ($method) {
            return (preg_match('/^action.{2,}/', $method->name));
        });
        $actionNames = array_filter($actions, function ($action) use ($controllerId) {
            $methodName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', str_replace('action', '', $action->name)));
            if ($methodName === $controllerId) {
                return $action->name;
            }
        });

        if (!count($actionNames)) {
            return;
        }

        $methodName = array_slice($actionNames, 0, 1)[0]->name;

        preg_match('/@return ([a-z]+)/i', $controllerInfo->getMethod($methodName)->getDocComment(), $regularResult);
        $formName = $regularResult[1];
        if (!$formName) {
            return;
        }
        $formNameSpase = $useStatements[strtolower($formName)];
        $this->getFieldFromForm($formNameSpase, $methodName);
        $this->setPaths($methodName, $contoller->id);
        $this->setTags($contoller->id);
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

    protected function setTags($controllerId)
    {
        if (!array_search($controllerId, $this->controllers)) {
            $this->controllers[] = $controllerId;
            $this->tags = [
                'name' => $controllerId,
            ];
        }
    }

    protected function setPaths(string $methodName, string $controllerId)
    {
        $this->paths = [
            '/' . $methodName => [
                'post' => [
                    'tags' => [
                        $controllerId
                    ],
                    'produces' => [
                        'application/json'
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'successful operation',
                            'schema' => [
                                '$ref' => '#/definitions/items'
                            ],
                        ],
                        '400' => [
                            'description' => 'Invalid username supplied'
                        ],
                        '404' => [
                            'description' => 'User not found'
                        ],
                    ]
                ]
            ]
        ];
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