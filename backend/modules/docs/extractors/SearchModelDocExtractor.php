<?php

namespace steroids\modules\docs\extractors;

use steroids\base\Model;
use steroids\base\SearchModel;

/**
 * @property-read string $definitionName
 */
class SearchModelDocExtractor extends BaseDocExtractor
{
    /**
     * @var SearchModel
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
        /** @var SearchModel $searchModel */
        $searchClassName = $this->className;
        $searchModel = new $searchClassName();

        /** @var Model $modelClassName */
        $modelClassName = $searchModel->createQuery()->modelClass;
        $model = new $modelClassName();

        $responses = [
            200 => [
                'description' => 'Successful operation',
                'schema' => [
                    'ref' => '#/definitions/' . $this->definitionName,
                ],
            ],
            400 => [
                'description' => 'Successful operation',
            ],
        ];
        foreach ($searchModel->fields() as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $key = $value;
            }

            /*if (is_string($value)) {

            } elseif (is_callable($value)) {
                // TODO
            }


            $fieldMetaDataForm = $this->searcFieldFromModel($formClass, $field);
            if ($fieldMetaData !== null) {
                $docs[$field] = $fieldMetaDataForm;
                continue;
            }
            $fieldMetaDataModel = $this->searcFieldFromModel($model, $field);
            $docs[$field] = $fieldMetaDataModel;*/
        }
        //$this->setModels($docs);
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

