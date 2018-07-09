<?php

namespace steroids\base;

use steroids\widgets\ActiveForm;
use steroids\widgets\Crud;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;

abstract class CrudController extends Controller
{
    public static $modelClass;
    public static $searchModelClass;

    public static function siteMapCrud($controllerId, $baseUrl)
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $requestParamName = $modelClass::getRequestParamName();

        return [
            'url' => ["$controllerId/index"],
            'urlRule' => $baseUrl,
            'items' => [
                'create' => [
                    'label' => \Yii::t('steroids', 'Добавление'),
                    'url' => ["$controllerId/create"],
                    'urlRule' => "$baseUrl/create",
                    'visible' => in_array('create', static::controls()),
                ],
                'update' => [
                    'label' => \Yii::t('steroids', 'Редактирование'),
                    'url' => ["$controllerId/update"],
                    'urlRule' => "$baseUrl/<$requestParamName:\d+>/update",
                    'visible' => in_array('update', static::controls()),
                ],
                'view' => [
                    'label' => \Yii::t('steroids', 'Просмотр'),
                    'url' => ["$controllerId/view"],
                    'urlRule' => "$baseUrl/<$requestParamName:\d+>/view",
                    'visible' => in_array('view', static::controls()),
                ],
            ],
        ];
    }

    public static function controls()
    {
        return [
            'create',
            'update',
            'delete',
        ];
    }

    public function searchFields()
    {
        return [];
    }

    public function columns()
    {
        return [];
    }

    public function fields()
    {
        return [];
    }

    public function details()
    {
        return [];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            // Fetch list items on pagination or filter form changes
            if (Yii::$app->request->isPost) {
                $searchModel = $this->createSearch();
                $searchModel->search(Yii::$app->request->post());
                return $searchModel;
            }

            // Delete
            if (Yii::$app->request->isDelete) {
                $this->onDelete(Yii::$app->request->post('id'));
                return [];
            }
        }

        return $this->renderCrud();
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            /** @var Model $modelClass */
            $modelClass = static::$modelClass;

            // Create
            $model = new $modelClass();
            $this->onCreate($model, Yii::$app->request->post());
            return ActiveForm::renderAjax($model);
        }

        return $this->renderCrud();
    }

    public function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {
            /** @var Model $modelClass */
            $modelClass = static::$modelClass;

            // Get primary key from post
            $primaryKey = $modelClass::primaryKey()[0];
            $id = Yii::$app->request->post($primaryKey);

            // Fetch form initial values
            if (Yii::$app->request->isGet) {
                $idParam = $modelClass::getRequestParamName();
                return $this->findModel(Yii::$app->request->get($idParam))
                    ->toFrontend(array_merge($this->fields(), [$primaryKey]));
            }

            // Update
            if (Yii::$app->request->isPost) {
                $model = $this->findModel($id);
                $this->onUpdate($model, Yii::$app->request->post());
                return ActiveForm::renderAjax($model, '');
            }

            // Delete
            if (Yii::$app->request->isDelete) {
                $this->onDelete($this->findModel($id));
                return [];
            }
        }

        return $this->renderCrud();
    }

    /**
     * @param Model $model
     * @param array $data
     * @throws \Exception
     */
    protected function onCreate($model, $data)
    {
        if ($model->load($data, '') && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('steroids', 'Запись добавлена'));
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @throws \Exception
     */
    protected function onUpdate(Model $model, $data)
    {
        if ($model->load($data, '') && $model->canUpdate(Yii::$app->user->model) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('steroids', 'Запись сохранена'));
        }
    }

    protected function onDelete(Model $model)
    {
        if ($model->canDelete(Yii::$app->user->model) && $model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('steroids', 'Запись удалена'));
        }
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

    /**
     * @param int|string $id
     * @return null|Model
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        /** @var Model $modelClass */
        $modelClass = static::$modelClass;
        $primaryKey = $modelClass::primaryKey()[0];
        return $modelClass::findOrPanic([$primaryKey => $id]);
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

    protected function renderCrud()
    {
        \Yii::$app->frontendState->set('config.store.history.basename', Url::to(['index']));

        return $this->renderContent(Crud::widget([
            'model' => static::$modelClass,
            'searchModel' => static::$searchModelClass,
            'controls' => static::controls(),
            'searchFields' => $this->searchFields(),
            'columns' => $this->columns(),
            'formFields' => $this->fields(),
            'viewAttributes' => $this->details(),
        ]));
    }
}
