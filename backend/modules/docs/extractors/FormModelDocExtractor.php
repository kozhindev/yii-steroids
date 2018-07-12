<?php

namespace steroids\modules\docs\extractors;

use steroids\base\FormModel;

/**
 * @property-read string $definitionName
 */
class FormModelDocExtractor extends BaseDocExtractor
{
    /**
     * @var FormModel
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

        $parameters = [];
        foreach ($model->safeAttributes() as $attribute) {
            $parameters[] = [
                'name' => $attribute,
                'in' => 'query',
                'description' => $model->getAttributeLabel($attribute),
                'required' => $model->isAttributeRequired($attribute),
                'type' => 'string', // TODO
            ];
        }

        $this->swaggerJson->updatePath($this->url, $this->method, [
            'parameters' => $parameters,
            'responses' => [
                200 => [
                    'description' => 'Successful operation',
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

