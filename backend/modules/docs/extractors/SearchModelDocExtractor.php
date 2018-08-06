<?php

namespace steroids\modules\docs\extractors;

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

        /** @var Model $modelClassName */
        $modelClassName = $searchModel->createQuery()->modelClass;
        $model = new $modelClassName();

        $required = [];
        $requestProperties = $this->getRequestProperties($searchModel, $required);
        $responseProperties = $this->getResponseProperties($modelClassName, $searchModel->fields());

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
                        'properties' => [
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
                                'items' => [
                                    'type' => 'object',
                                    'properties' => $responseProperties,
                                ],
                            ],
                        ],
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

