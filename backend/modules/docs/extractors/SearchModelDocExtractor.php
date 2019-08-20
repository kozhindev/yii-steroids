<?php

namespace steroids\modules\docs\extractors;

use steroids\base\BaseSchema;
use steroids\base\Model;
use steroids\base\SearchModel;

/**
 * @property-read string $definitionName
 */
class SearchModelDocExtractor extends FormModelDocExtractor
{
    public function run()
    {
        /** @var SearchModel $searchModel */
        $searchClassName = $this->className;
        $searchModel = new $searchClassName();

        /** @var Model|BaseSchema $modelClassName */
        $modelClassName = $searchModel->fieldsSchema() ?: $searchModel->createQuery()->modelClass;
        $modelObject = new $modelClassName();

        $required = [];
        $requestSchema = SwaggerTypeExtractor::getInstance()->extractModel($this->className, $this->getRequestFields($searchModel, $required));
        $responseSchema = is_subclass_of($modelObject, BaseSchema::class)
            ? SwaggerTypeExtractor::getInstance()->extractSchema($modelClassName, $modelObject->fields())
            : SwaggerTypeExtractor::getInstance()->extractModel($modelClassName, $searchModel->fields());

        $responseProperties = [
            'meta' => [
                'description' => 'Additional meta information',
                'type' => 'object',
            ],
            'total' => [
                'description' => 'Total items count',
                'type' => 'number',
            ],
            'items' => [
                'description' => 'Fined items',
                'type' => 'array',
                'items' => $responseSchema,
            ],
        ];

        $requestSchema['properties'] = array_merge($requestSchema['properties'], [
            'page' => [
                'description' => 'Page',
                'type' => 'number',
            ],
            'pageSize' => [
                'description' => 'Page size',
                'type' => 'number',
            ],
        ]);

        $this->swaggerJson->updatePath($this->url, $this->method, [
            'parameters' => [
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
                    'schema' => empty($responseSchema) ? null : [
                        'type' => 'object',
                        'properties' => $responseProperties,
                    ],
                ],
                400 => [
                    'description' => 'Validation errors',
                    'schema' => [
                        'type' => 'object',
                        'properties' => array_merge(
                            $responseProperties,
                            [
                                'errors' => [
                                    'type' => 'object',
                                ],
                            ]
                        ),
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

