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
            ];

            /** @var Type $appType */
            $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
            $appType->prepareSwaggerProperty($className, $attribute, $requestProperties[$attribute]);
        }

        $responseProperties = [];
        foreach ($model->fields() as $key => $attribute) {
            if (is_int($key) && is_string($attribute)) {
                $key = $attribute;
            }

            if (is_string($attribute)) {
                if (!$model->canGetProperty($attribute)) {
                    continue;
                }
                $responseProperties[$key] = [
                    'description' => $model->getAttributeLabel($attribute),
                ];

                /** @var Type $appType */
                $appType = \Yii::$app->types->getTypeByModel($model, $attribute);
                $appType->prepareSwaggerProperty($className, $attribute, $responseProperties[$key]);
            }
        }

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
}

