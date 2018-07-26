<?php

namespace steroids\modules\docs\extractors;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Type;
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
        $responseProperties = $this->getResponseProperties($model, $model->fields());

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
     * @param Model|FormModel $model
     * @param array $fields
     * @return array
     */
    protected function getResponseProperties($model, $fields)
    {
        $meta = $model::meta();

        $responseProperties = [];
        foreach ($fields as $key => $attribute) {
            if (is_int($key) && is_string($attribute)) {
                $key = $attribute;
            }

            if (is_string($attribute)) {
                if (!$model->canGetProperty($attribute)) {
                    continue;
                }
                $responseProperties[$key] = [
                    'description' => $model->getAttributeLabel($attribute),
                    'example' => ArrayHelper::getValue($meta, [$attribute, 'example']),
                ];

                /** @var Type $appType */
                $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
                $appType->prepareSwaggerProperty(get_class($model), $attribute, $responseProperties[$key]);
            }
        }
        return $responseProperties;
    }
}

