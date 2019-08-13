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
    public static $viewSchema;

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
                        'urlRule' => "PUT,POST $baseUrl/<$idParam>",
                        'visible' => in_array('update', $controls),
                    ],
                    'view' => [
                        'label' => \Yii::t('steroids', 'Просмотр'),
                        'url' => ['view'],
                        'urlRule' => "GET $baseUrl/<$idParam>",
                        'visible' => in_array('view', $controls),
                    ],
                    'delete' => [
                        'label' => \Yii::t('steroids', 'Удаление'),
                        'url' => ['delete'],
                        'urlRule' => "DELETE $baseUrl/<$idParam>",
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
        $searchModel->search(Yii::$app->request->get());
        return $searchModel;
    }

    public function actionCreate()
    {
        /** @var Model $model */
        $model = new static::$modelClass();
        $user = Yii::$app->user->model;

        if (!$model->canCreate($user)) {
            throw new ForbiddenHttpException();
        }

        $this->loadModel($model);
        $this->saveModel($model);

        if ($errors = $model->getErrors()) {
            $result = ['errors' => $errors];
        } else {
            if (static::$viewSchema) {
                $result = new static::$viewSchema(['model' => $model]);
            } else {
                $result = $model;
            }
        }

        return $result;
    }

    public function actionUpdate()
    {
        $model = $this->findModel();
        $user = Yii::$app->user->model;

        if (!$model->canUpdate($user)) {
            throw new ForbiddenHttpException();
        }

        $this->loadModel($model);
        $this->saveModel($model);

        if ($errors = $model->getErrors()) {
            $result = ['errors' => $errors];
        } else {
            if (static::$viewSchema) {
                $result = new static::$viewSchema(['model' => $model]);
            } else {
                $result = $model;
            }
        }

        return $result;
    }

    public function actionView()
    {
        $model = $this->findModel();
        if (!$model->canView(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        if (static::$viewSchema) {
            return new static::$viewSchema(['model' => $model]);
        }

        return $model;
    }

    public function actionDelete()
    {
        $model = $this->findModel();
        if (!$model->canDelete(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        $model->deleteOrPanic();

        return $model;
    }

    protected function loadModel($model) {
        $model->load(Yii::$app->request->post(), '');
    }

    /**
     * @param Model $model
     * @throws
     */
    protected function saveModel($model)
    {
        if ($model->isNewRecord) {
            $permittedAttributes = $model->canCreate(Yii::$app->user->model);
        } else {
            $permittedAttributes = $model->canUpdate(Yii::$app->user->model);
        }

        if ($permittedAttributes) {
            $model->save($permittedAttributes);
        }
    }


    /**
     * @return Model|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel()
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;

        // Get primary key from post
        $primaryKey = $modelClass::primaryKey()[0];
        $id = Yii::$app->request->get($modelClass::getRequestParamName());
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

        if (empty($attributes)) {
            $attributes = ['*'];
        }

        // Append primary key
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $attributes[] = $modelClass::primaryKey()[0];

        return $attributes;
    }

    public function afterAction($action, $result)
    {
        if (Yii::$app->request->get('scope') === SearchModel::SCOPE_PERMISSIONS
            && ($result instanceof BaseSchema || $result instanceof Model)
        ) {
            $user = Yii::$app->user->model;
            $model = $result instanceof BaseSchema ? $result->model : $result;

            $result = array_merge(
                $result->toFrontend(null, $user),
                $model->getPermissions($user)
            );
        }

        return parent::afterAction($action, $result);
    }

}
