<?php

namespace steroids\modules\docs\extractors;

use steroids\base\BaseSchema;
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
     * @var FormModel|Model|BaseSchema
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
        $fields = array_merge($this->listenRelations, $this->getRequestFields($model, $required));
        $requestSchema = SwaggerTypeExtractor::getInstance()->extractModelRequest($this->className, $fields);
        $responseSchema = SwaggerTypeExtractor::getInstance()->extractObject($this->className, $model->fields());

        $this->swaggerJson->updatePath($this->url, $this->method, [
            'parameters' => empty($requestSchema) ? null : [
                [
                    'in' => 'body',
                    'name' => 'request',
                    'schema' => array_merge($requestSchema, [
                        'required' => $required,
                    ]),
                ],
            ],
            'responses' => [
                200 => [
                    'description' => 'Successful operation',
                    'content' => [
                        'application/json' => [
                            'schema' => $responseSchema,
                        ],
                    ],
                ],
                400 => [
                    'description' => 'Validation errors',
                    'content' => [
                        'application/json' => [
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
    protected function getRequestFields($model, &$required)
    {
        if ($model instanceof BaseSchema) {
            return [];
        }

        $requestFields = [];
        if (strtoupper($this->method) !== 'GET' || !($model instanceof Model)) {
            foreach ($model->safeAttributes() as $attribute) {
                // Skip read only params
                if (!$model->canSetProperty($attribute)) {
                    continue;
                }

                // Skip params from url
                if (stripos($this->url, '{' . $attribute . '}') !== false) {
                    continue;
                }

                // Store required attributes
                if ($model->isAttributeRequired($attribute)) {
                    $required[] = $attribute;
                }
                $requestFields[] = $attribute;
            }
        }
        return $requestFields;
    }

}

