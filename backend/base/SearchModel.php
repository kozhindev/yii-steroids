<?php

namespace steroids\base;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SearchModel extends FormModel
{
    const SCOPE_PERMISSIONS = 'permissions';
    const SCOPE_MODEL = 'model';

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
     * @var string|string[]
     */
    public $scope;

    /**
     * @var string|Model
     */
    public $model;

    /**
     * Context user model
     * @var Model
     */
    public $user;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var ArrayDataProvider|ActiveDataProvider
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
        $this->scope = ArrayHelper::getValue($params, 'scope', $this->scope);
        if (!is_array($this->scope)) {
            $this->scope = explode(',', $this->scope ?: '');
        }
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

    public function getItems($fields = null)
    {
        $schema = $this->fieldsSchema();
        if ($schema) {
            return Model::anyToFrontend(
                array_map(
                    function ($model) use ($schema) {
                        return $this->createSchema($schema, $model);
                    },
                    $this->dataProvider->models
                )
            );
        }

        $fields = $fields ?: $this->fields();
        return Model::anyToFrontend($this->dataProvider->models, $fields);
    }

    public function toFrontend($fields = null)
    {
        $items = $this->getItems();

        // Append permissions
        if (in_array(self::SCOPE_PERMISSIONS, $this->scope)) {
            $user = $this->user ?: (\Yii::$app->has('user') ? \Yii::$app->user->identity : null);
            if ($user) {
                $info = new \ReflectionClass($this->dataProvider->query->modelClass);
                $cans = [];
                foreach ($info->getMethods() as $method) {
                    $parameters = $method->getParameters();
                    if (count($parameters) === 0 || $parameters[0]->getName() !== 'user') {
                        continue;
                    }

                    $name = $method->getName();
                    if (preg_match('/^can(.+)$/', $name)) {
                        $cans[] = $name;
                    }
                }

                $models = $this->dataProvider->models;
                foreach ($items as $index => &$item) {
                    foreach ($cans as $can) {
                        $item[$can] = $models[$index]->$can($user);
                    }
                }
            }
        }

        // Append meta
        if (in_array(self::SCOPE_MODEL, $this->scope) && $this->dataProvider instanceof ActiveDataProvider) {
            /** @var ActiveQuery $query */
            $query = $this->dataProvider->query;
            $this->meta['model'] = str_replace('.', '\\', $query->modelClass);
            if (get_class($this) !== __CLASS__) {
                $this->meta['searchModel'] = str_replace('.', '\\', get_class($this));
            }
        }

        // Result
        $result = [
            'meta' => !empty($this->meta) ? $this->meta : null,
            'total' => $this->dataProvider->getTotalCount(),
            'items' => $items,
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
     * @return null|string
     */
    public function fieldsSchema()
    {
        return null;
    }

    /**
     * @param BaseSchema $schema
     * @param Model $model
     * @return BaseSchema
     */
    public function createSchema($schema, $model)
    {
        return new $schema(['model' => $model]);
    }
}
