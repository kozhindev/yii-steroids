<?php

namespace steroids\widgets;

use steroids\base\Model;
use steroids\base\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class GridView extends Widget
{
    const LIST_ID_PREFIX = 'GridView_';

    /**
     * @var array
     */
    public $actionParams = [];

    /**
     * @var array
     */
    public $columns;

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @var ActiveDataProvider
     */
    public $dataProvider;

    public function init()
    {
        parent::init();

        // Normalize columns
        foreach ($this->columns as $key => $field) {
            if (is_string($field)) {
                $this->columns[$key] = ['attribute' => $field];
            }
            if (!isset($field['attribute']) && is_string($key)) {
                $this->columns[$key]['attribute'] = $key;
            }
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        return $this->renderReact([
            'listId' => self::LIST_ID_PREFIX . $this->id,
            'columns' => $this->getColumnsConfig(),
            'actions' => $this->getActionsConfig(),
            'items' => $this->getItems(),
            'total' => $this->dataProvider->totalCount,
            'loadMore' => false,
            'defaultPageSize' => ArrayHelper::getValue($this->dataProvider, 'pagination.pageSize'),
            'paginationProps' => [
                'pageParam' => ArrayHelper::getValue($this->dataProvider, 'pagination.pageParam'),
            ],
        ], false);
    }

    protected function getColumnsConfig()
    {
        $config = [];
        foreach ($this->columns as $column) {
            $attribute = ArrayHelper::getValue($column, 'attribute');
            if ($attribute && $this->dataProvider instanceof ActiveDataProvider) {
                $modelClass = $this->dataProvider->query->modelClass;

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
            }

            $config[] = $column;
        }
        return $config;
    }

    protected function getActionsConfig()
    {
        $config = [];
        foreach ($this->actions as $action) {
            if (is_string($action)) {
                $action = ['id' => $action];
            }

            // TODO
            /*if (!isset($action['visible']) || $action['visible'] === true) {
                $siteMapItem = \Yii::$app->siteMap->getItem($action['url']);
                $action['visible'] = $siteMapItem && \Yii::$app->authManager->checkMenuAccess(\Yii::$app->user->model, $siteMapItem);
            }*/
            $config[] = $action;
        }
        return $config;
    }

    protected function getItems()
    {
        $items = [];
        foreach ($this->dataProvider->getModels() as $index => $model) {
            $row = [];
            foreach ($this->columns as $column) {
                $attribute = ArrayHelper::getValue($column, 'attribute');

                // Check direct value render
                $valueCallback = ArrayHelper::getValue($column, 'value');
                if ($valueCallback && is_callable($valueCallback)) {
                    $row[$attribute] = call_user_func($valueCallback, $model, $attribute, $index, $this);
                }

                // Prepare values by type
                $type = \Yii::$app->types->getTypeByModel($model, $attribute);
                $type->prepareViewValue($model, $attribute, $row);

                // Add can* params
                foreach ($this->actions as $action) {
                    $actionId = is_string($action) ? $action : $action['id'];
                    $key = 'can' . ucfirst($actionId);
                    if (method_exists($model, $key)) {
                        $row[$key] = $model->$key(\Yii::$app->user->model);
                    }
                }
            }
            $items[] = $row;
        }
        return $items;
    }

}