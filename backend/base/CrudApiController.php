<?php

namespace steroids\base;

use Yii;
use steroids\widgets\Crud;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

abstract class CrudApiController extends Controller
{
    public static $modelClass;
    public static $searchModelClass;

    /**
     * @param $baseUrl
     * @param array $custom
     * @return array
     * @throws \ReflectionException
     */
    public static function apiMapCrud($baseUrl, $custom = [])
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $idParam = $modelClass::getRequestParamName();
        $controls = static::controls();

        $reflectionInfo = new \ReflectionClass($modelClass);

        return ArrayHelper::merge(
            [
                'label' => $reflectionInfo->getShortName(),
                'items' => [
                    'index' => [
                        'label' => \Yii::t('steroids', 'Список'),
                        'url' => ['index'],
                        'urlRule' => "GET $baseUrl",
                        'visible' => in_array('create', $controls),
                    ],
                    'create' => [
                        'label' => \Yii::t('steroids', 'Добавление'),
                        'url' => ['create'],
                        'urlRule' => "POST $baseUrl",
                        'visible' => in_array('create', $controls),
                    ],
                    'update' => [
                        'label' => \Yii::t('steroids', 'Редактирование'),
                        'url' => ['update'],
                        'urlRule' => "PUT $baseUrl/<$idParam:\d+>",
                        'visible' => in_array('update', $controls),
                    ],
                    'view' => [
                        'label' => \Yii::t('steroids', 'Просмотр'),
                        'url' => ['view'],
                        'urlRule' => "GET $baseUrl/<$idParam:\d+>",
                        'visible' => in_array('view', $controls),
                    ],
                    'delete' => [
                        'label' => \Yii::t('steroids', 'Удаление'),
                        'url' => ['delete'],
                        'urlRule' => "DELETE $baseUrl/<$idParam:\d+>",
                        'visible' => in_array('delete', $controls),
                    ],
                ],
            ],
            $custom
        );
    }

    public static function controls()
    {
        return [
            'index',
            'create',
            'update',
            'view',
            'delete',
        ];
    }

    public function columns()
    {
        return [];
    }

    public function actionIndex()
    {
        $searchModel = $this->createSearch();
        $searchModel->search(Yii::$app->request->post());
        return $searchModel;
    }

    public function actionCreate()
    {
        /** @var Model $model */
        $model = new static::$modelClass();

        if (!$model->canCreate(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        $model->load(Yii::$app->request->post(), '');
        $model->save();

        return $model;
    }

    public function actionUpdate()
    {
        $model = $this->findModel();
        if (!$model->canUpdate(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        $model->load(Yii::$app->request->post(), '');
        $model->save();

        return $model;
    }

    public function actionView()
    {
        $model = $this->findModel();
        if (!$model->canView(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        return $model;
    }

    public function actionDelete()
    {
        $model = $this->findModel();
        if (!$model->canDelete(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        return $model;
    }

    protected function findModel()
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;

        // Get primary key from post
        $primaryKey = $modelClass::primaryKey()[0];
        $id = Yii::$app->request->post($modelClass::getRequestParamName());
        return $modelClass::findOrPanic([$primaryKey => $id]);
    }


    /**
     * @return SearchModel
     */
    protected function createSearch()
    {
        if (static::$searchModelClass) {
            $modelClass = static::$searchModelClass;
            return new $modelClass([
                'fields' => $this->getListAttributes(),
            ]);
        } else {
            return new SearchModel([
                'model' => static::$modelClass,
                'fields' => $this->getListAttributes(),
            ]);
        }
    }

    protected function getListAttributes()
    {
        $attributes = [];
        foreach (Crud::normalizeConfig($this->columns()) as $column) {
            if (isset($column['value']) && is_callable($column['value'])) {
                $attributes[$column['attribute']] = $column['value'];
            } else {
                $attributes[] = $column['attribute'];
            }
        }

        // Append primary key
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $attributes[] = $modelClass::primaryKey()[0];

        // Append access flags
        foreach (Crud::normalizeConfig(static::controls(), 'id') as $action) {
            $method = 'can' . ucfirst($action['id']);
            if (method_exists($modelClass, $method)) {
                $attributes[$method] = function (Model $model) use ($method) {
                    return $model->$method(Yii::$app->user->model);
                };
            }
        }

        return $attributes;
    }

}
