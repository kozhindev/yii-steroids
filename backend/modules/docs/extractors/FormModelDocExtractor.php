<?php

namespace steroids\modules\docs\extractors;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Type;
use steroids\modules\docs\helpers\ExtractorHelper;
use yii\helpers\ArrayHelper;

/**
 * @property-read string $definitionName
 */
class FormModelDocExtractor extends BaseDocExtractor
{
    /**
     * @var FormModel|Model
     */
    public $className;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $method;

    public function run()
    {
        /** @var FormModel $model */
        $className = $this->className;
        $model = new $className();

        $required = [];
        $requestProperties = $this->getRequestProperties($model, $required);
        $responseProperties = $this->getResponseProperties($this->className, $model->fields());

        $this->swaggerJson->updatePath($this->url, $this->method, [
            'parameters' => empty($requestProperties) ? null : [
                [
                    'in' => 'body',
                    'name' => 'request',
                    'schema' => [
                        'type' => 'object',
                        'required' => $required,
                        'properties' => $requestProperties,
                    ],
                ],
            ],
            'responses' => [
                200 => [
                    'description' => 'Successful operation',
                    'schema' => empty($responseProperties) ? null : [
                        'type' => 'object',
                        'properties' => $responseProperties,
                    ],
                ],
                400 => [
                    'description' => 'Validation errors',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'errors' => [
                                'type' => 'object',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getDefinitionName()
    {
        return (new \ReflectionClass($this->className))->getShortName();
    }

    /**
     * @param Model|FormModel $model
     * @param array $required
     * @return array
     */
    protected function getRequestProperties($model, &$required)
    {
        $meta = $model::meta();

        $requestProperties = [];
        foreach ($model->safeAttributes() as $attribute) {
            if (!$model->canSetProperty($attribute)) {
                continue;
            }
            if ($model->isAttributeRequired($attribute)) {
                $required[] = $attribute;
            }
            $requestProperties[$attribute] = [
                'description' => $model->getAttributeLabel($attribute),
                'example' => ArrayHelper::getValue($meta, [$attribute, 'example']),
            ];

            /** @var Type $appType */
            $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
            $appType->prepareSwaggerProperty(get_class($model), $attribute, $requestProperties[$attribute]);
        }
        return $requestProperties;
    }

    /**
     * @param string|Model|FormModel $rootModelClass
     * @param array $fields
     * @return array
     * @throws \ReflectionException
     */
    protected function getResponseProperties($rootModelClass, $fields)
    {
        $responseProperties = [];
        foreach ($fields as $key => $attributes) {
            if (is_int($key) && is_string($attributes)) {
                $key = $attributes;
            }

            if (is_string($attributes)) {
                $attributes = explode('.', $attributes);
            }

            if (is_callable($attributes)) {
                $callablePhpType = ExtractorHelper::findCallableType($rootModelClass, $attributes);
                if (class_exists($callablePhpType)) {
                    //$callableModel = new $callablePhpType();
                    // TODO
                    // TODO
                    // TODO
                    // TODO
                }

                continue;
            }

            if (is_array($attributes)) {
                $modelClass = $rootModelClass;
                foreach ($attributes as $attribute) {
                    $phpType = ExtractorHelper::findPhpDocType($modelClass, $attribute);
                    if ($phpType && class_exists($phpType)) {
                        $modelClass = $phpType;
                        continue;
                    }

                    $responseProperties[$key] = [
                        'type' => 'string',
                    ];

                    if (ExtractorHelper::isModelAttribute($modelClass, $attribute)) {
                        /** @var Model|FormModel $model */
                        $model = new $modelClass();
                        $responseProperties[$key] = array_merge($responseProperties[$key], [
                            'description' => $model->getAttributeLabel($attribute),
                            'example' => ArrayHelper::getValue($modelClass::meta(), [$attribute, 'example']),
                        ]);

                        /** @var Type $appType */
                        $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
                        $appType->prepareSwaggerProperty(get_class($model), $attribute, $responseProperties[$key]);
                    }
                }
            }
        }
        return $responseProperties;
    }
}

