<?php

namespace steroids\widgets;

use steroids\base\SearchModel;
use Yii;
use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Crud extends Widget
{
    const CRUD_ID_PREFIX = 'SteroidsCrud';
    const ID_TEMPLATE = '#ID#';

    /**
     * @var string|Model|FormModel
     */
    public $model;

    /**
     * @var string|FormModel
     */
    public $searchModel;

    /**
     * @var string[]|array
     */
    public $controls;

    /**
     * @var string[]|array
     */
    public $searchFields;

    /**
     * @var string[]|array
     */
    public $formFields;

    /**
     * @var string[]|array
     */
    public $columns;

    /**
     * @var string[]|array
     */
    public $viewAttributes;

    /**
     * @var string
     */
    public $view;

    /**
     * @var array
     */
    public $listProps;

    /**
     * @var array
     */
    public $formProps;

    /**
     * @param array $config
     * @param string $name
     * @return array
     */
    public static function normalizeConfig($config, $name = 'attribute')
    {
        $config = array_filter((array) $config);
        foreach ($config as $key => $value) {
            if (is_string($value)) {
                $config[$key] = [$name => $value];
            }
            if (is_callable($value) && is_string($key)) {
                $config[$key] = [
                    $name => $key,
                    'value' => $value,
                ];
            }
            if (is_array($value) && !isset($value[$name]) && is_string($key)) {
                $config[$key][$name] = $key;
            }
        }
        return $config;
    }

    public function init()
    {
        parent::init();

        // Normalize fields
        $this->controls = static::normalizeConfig($this->controls, 'id');
        $this->searchFields = static::normalizeConfig($this->searchFields);
        $this->formFields = static::normalizeConfig($this->formFields);
        $this->columns = static::normalizeConfig($this->columns);
        $this->viewAttributes = static::normalizeConfig($this->viewAttributes);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $modelClass = $this->model;

        return $this->renderReact([
            'crudId' => self::CRUD_ID_PREFIX . $this->id,
            'requestParamName' => $modelClass::getRequestParamName(),
            'controls' => $this->getControls(),
            'listProps' => ArrayHelper::merge(
                [
                    'action' => '',
                    'actions' => $this->controls,
                    'columns' => $this->getColumns(),
                    'searchForm' => !empty($this->searchFields) || $this->searchModel
                        ? [
                            'layout' => 'horizontal',
                            'fields' => $this->getSearchFields(),
                        ]
                        : null,
                ],
                $this->listProps ?: []
            ),
            'formProps' => ArrayHelper::merge(
                [
                    'layout' => 'horizontal',
                    'fields' => $this->getFields(),
                ],
                $this->formProps ?: []
            ),
        ], false);
    }

    /**
     * @return array
     */
    protected function getControls()
    {
        $controls = array_values($this->controls);
        foreach ($controls as &$item) {
            if (isset($item['url']) && is_array($item['url'])) {
                $item['url'] = Url::to($item['url']);
            }
        }
        return $controls;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        $config = [];
        foreach ($this->columns as $column) {
            $attribute = ArrayHelper::getValue($column, 'attribute');
            $modelClass = is_object($this->model) ? get_class($this->model) : $this->model;

            /** @var Model $model */
            $model = new $modelClass();
            $column = array_merge(
                [
                    'label' => $model->getAttributeLabel($attribute),
                    'hint' => $model->getAttributeHint($attribute),
                ],
                $column
            );

            // Prepare column props by type
            $type = \Yii::$app->types->getTypeByModel($modelClass, $attribute);
            $type->prepareFormatterProps($model, $attribute, $column, $import);

            // Skip value func
            unset($column['value']);

            $config[] = $column;
        }
        return $config;
    }

    protected function getSearchFields()
    {
        $config = [];
        $searchFields = $this->searchFields;
        if (empty($searchFields) && $this->searchModel) {
            /** @var SearchModel $modelClass */
            $modelClass = $this->searchModel;
            $searchFields = (new $modelClass())->attributes();
        }

        foreach ($this->searchFields as $field) {
            if ($this->searchModel) {
                $attribute = $field['attribute'];
                $modelClass = is_object($this->searchModel) ? get_class($this->searchModel) : $this->searchModel;

                $model = new $modelClass();
                $field = array_merge(
                    [
                        'label' => $model->getAttributeLabel($attribute),
                        'hint' => $model->getAttributeHint($attribute),
                        'required' => $model->isAttributeRequired($attribute),
                    ],
                    $field
                );

                $type = \Yii::$app->types->getTypeByModel($modelClass, $attribute);
                $type->prepareFieldProps($model, $attribute, $field, $import);
            }

            $config[] = $field;
        }
        return $config;
    }

    protected function getFields()
    {
        $config = [];
        foreach ($this->formFields as $field) {
            $attribute = $field['attribute'];
            $modelClass = is_object($this->model) ? get_class($this->model) : $this->model;

            $model = new $modelClass();
            $field = array_merge(
                [
                    'label' => $model->getAttributeLabel($attribute),
                    'hint' => $model->getAttributeHint($attribute),
                    'required' => $model->isAttributeRequired($attribute),
                ],
                $field
            );

            $type = \Yii::$app->types->getTypeByModel($modelClass, $attribute);
            $type->prepareFieldProps($model, $attribute, $field, $import);

            $config[] = $field;
        }
        return $config;
    }

}