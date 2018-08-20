<?php

namespace steroids\modules\docs\extractors;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\SearchModel;
use steroids\modules\docs\helpers\ExtractorHelper;
use steroids\modules\docs\models\SwaggerJson;
use yii\base\BaseObject;

abstract class BaseDocExtractor extends BaseObject
{
    /**
     * @var SwaggerJson
     */
    public $swaggerJson;

    public function createTypeExtractor($type, $url, $method)
    {
        if (ExtractorHelper::isPrimitiveType($type)) {
            // TODO
        } else if (class_exists($type)) {
            if (is_subclass_of($type, SearchModel::class)) {
                return new SearchModelDocExtractor([
                    'swaggerJson' => $this->swaggerJson,
                    'className' => $type,
                    'url' => $url,
                    'method' => $method,
                ]);
            }
            if (is_subclass_of($type, FormModel::class) || is_subclass_of($type, Model::class)) {
                return new FormModelDocExtractor([
                    'swaggerJson' => $this->swaggerJson,
                    'className' => $type,
                    'url' => $url,
                    'method' => $method,
                ]);
            }
        }
    }

    abstract function run();
}




