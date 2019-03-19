<?php

namespace steroids\base;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SearchModel extends FormModel
{
    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var int
     */
    public $pageSize = 50;

    /**
     * @var array
     */
    public $sort;

    /**
     * @var string|Model
     */
    public $model;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var ArrayDataProvider
     */
    public $dataProvider;

    /**
     * @var array
     */
    public $meta = [];

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $this->page = ArrayHelper::getValue($params, 'page', $this->page);
        $this->pageSize = ArrayHelper::getValue($params, 'pageSize', $this->pageSize);
        $this->sort = ArrayHelper::getValue($params, 'sort', $this->sort);
        $this->load($params);

        $query = $this->createQuery();
        if ($this->validate()) {
            $this->prepare($query);
        } elseif ($query instanceof Query) {
            $query->emulateExecution();
        }

        $this->dataProvider = $this->createProvider();
        if (is_array($this->dataProvider)) {
            $this->dataProvider = new ActiveDataProvider(ArrayHelper::merge(
                [
                    'query' => $query,
                    'sort' => false,
                    'pagination' => [
                        'page' => $this->page - 1,
                        'pageSize' => $this->pageSize,
                        'pageSizeLimit' => [1, 500],
                    ],
                ],
                $this->dataProvider
            ));
        } else if ($this->dataProvider instanceof ActiveDataProvider) {
            $this->dataProvider->query = $query;
        }

        return $this->dataProvider;
    }

    /**
     * @return ActiveQuery
     */
    public function createQuery()
    {
        $modelClass = $this->model;
        return $modelClass::find();
    }

    public function formName()
    {
        return '';
    }

    /**
     * @return ActiveDataProvider|ArrayDataProvider|array
     */
    public function createProvider()
    {
        return [];
    }

    public function fields()
    {
        return $this->fields;
    }

    public function sortFields()
    {
        return [];
    }

    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function toFrontend($fields = null)
    {
        $fields = $fields ?: $this->fields();
        $result = [
            'meta' => !empty($this->meta) ? $this->meta : null,
            'total' => $this->dataProvider->getTotalCount(),
            'items' => $this->fieldsSchema()
                ? BaseSchema::anyToFrontend($this->fieldsSchema()::toList($this->dataProvider->models))
                : Model::anyToFrontend($this->dataProvider->models, $fields),
        ];
        if ($this->hasErrors()) {
            $result['errors'] = $this->getErrors();
        }
        return $result;
    }

    /**
     * @param ActiveQuery $query
     */
    public function prepare($query)
    {
        $sortFields = $this->sortFields();
        if (!empty($sortFields) && !empty($this->sort)) {
            foreach ((array)$this->sort as $key) {
                $direction = strpos($key, '!') === 0 ? SORT_DESC : SORT_ASC;
                $attribute = preg_replace('/^!/', '', $key);
                if (in_array($attribute, $sortFields)) {
                    $query->addOrderBy([$attribute => $direction]);
                }
            }
        }
    }

    /**
     * @return null|BaseSchema
     */
    public function fieldsSchema()
    {
        return null;
    }
}
