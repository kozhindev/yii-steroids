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

    public function getItems($fields = null, $user = null)
    {
        $user = $user ?: $this->user;
        $schema = $this->fieldsSchema();
        $fields = $fields ?: $this->fields();

        return array_map(
            function ($model) use ($schema, $fields, $user) {
                return
                    ($schema
                        ? $this->createSchema($schema, $model)
                        : $model
                    )->toFrontend($fields, $user);
            },
            $this->dataProvider->models
        );
    }

    public function toFrontend($fields = null, $user = null)
    {
        $user = $user ?:
            $this->user ?:
                (\Yii::$app->has('user') ? \Yii::$app->user->identity : null);

        $items = $this->getItems($fields, $user);

        // Append whole model permissions
        if (in_array(self::SCOPE_PERMISSIONS, $this->scope) && $user) {
            /** @var Model[] $models */
            $models = $this->dataProvider->models;
            foreach ($items as $index => $item) {
                $items[$index] = array_merge(
                    $items[$index],
                    $models[$index]->getPermissions($user)
                );
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
     * @param string $schema
     * @param Model $model
     * @return BaseSchema
     */
    public function createSchema($schema, $model)
    {
        return new $schema(['model' => $model]);
    }
}
