<?php

namespace steroids\base;

use Yii;
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
                    'update-batch' => [
                        'label' => \Yii::t('steroids', 'Множественное редактирование'),
                        'url' => ['update-batch'],
                        'urlRule' => "PUT,POST $baseUrl/update-batch",
                        'visible' => in_array('update-batch', $controls),
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
            'update-batch',
            'view',
            'delete',
        ];
    }

    public function fields()
    {
        return null;
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
        return $this->actionSave($model, Yii::$app->request->post());
    }

    public function actionUpdate()
    {
        $model = $this->findModel();
        return $this->actionSave($model, Yii::$app->request->post());
    }

    public function actionUpdateBatch()
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $primaryKey = $modelClass::primaryKey()[0];

        $result = [];
        foreach (Yii::$app->request->post() as $id => $data) {
            $model = $modelClass::findOrPanic([$primaryKey => $id]);
            $result[$id] = $this->actionSave($model, $data);
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
            $result = new static::$viewSchema(['model' => $model]);
        } else {
            $result = $model;
        }

        return $result;
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

    /**
     * @param Model $model
     * @param array $post
     * @return array
     * @throws ForbiddenHttpException
     */
    protected function actionSave($model, $post)
    {
        $attributes = $model->attributes();
        $permittedAttributes = $model->isNewRecord
            ? $model->canCreate(Yii::$app->user->model)
            : $model->canUpdate(Yii::$app->user->model);
        if (!$permittedAttributes) {
            throw new ForbiddenHttpException();
        }

        $data = [];
        foreach ($post as $key => $value) {
            if ($permittedAttributes === true || !in_array($key, $attributes) || in_array($key, $permittedAttributes)) {
                $data[$key] = $value;
            }
        }

        $this->loadModel($model, $data);
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

    /**
     * @param Model $model
     * @param array $data
     * @throws ForbiddenHttpException
     */
    protected function loadModel($model, $data)
    {
        $model->load($data, '');
    }

    /**
     * @param Model $model
     * @throws
     */
    protected function saveModel($model)
    {
        $model->save();
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
                'fields' => $this->fields(),
                'user' => Yii::$app->user->model,
            ]);
        } else {
            return new SearchModel([
                'model' => static::$modelClass,
                'fields' => $this->fields(),
                'user' => Yii::$app->user->model,
            ]);
        }
    }

    public function afterAction($action, $result)
    {
        if ($result instanceof BaseSchema || $result instanceof Model) {
            if (Yii::$app->request->get('scope') === SearchModel::SCOPE_PERMISSIONS) {
                $user = Yii::$app->user->model;
                $model = $result instanceof BaseSchema ? $result->model : $result;

                $result = array_merge(
                    $result->toFrontend($this->fields(), $user),
                    $model->getPermissions($user)
                );
            } else {
                $result = $result->toFrontend($this->fields());
            }
        }

        return parent::afterAction($action, $result);
    }

}
