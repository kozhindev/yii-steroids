<?php

namespace steroids\modules\gii\controllers;

use steroids\modules\gii\generators\enum\EnumGenerator;
use steroids\modules\gii\generators\formModel\FormModelGenerator;
use steroids\modules\gii\generators\model\ModelGenerator;
use steroids\modules\gii\generators\crud\CrudGenerator;
use steroids\modules\gii\generators\module\ModuleGenerator;
use steroids\modules\gii\GiiModule;
use steroids\modules\gii\models\ControllerClass;
use steroids\modules\gii\models\EnumClass;
use steroids\modules\gii\models\EnumMetaItem;
use steroids\modules\gii\models\FormModelClass;
use steroids\modules\gii\models\MetaItem;
use steroids\modules\gii\models\ModelClass;
use steroids\modules\gii\models\ModuleClass;
use steroids\modules\gii\models\Relation;
use steroids\modules\gii\widgets\GiiApplication\GiiApplication;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class GiiController extends Controller
{
    public static function coreMenuItems()
    {
        return [
            'gii' => [
                'label' => 'Генератор кода',
                'url' => ['/gii/gii/index'],
                'urlRule' => 'gii',
                'order' => 500,
                'accessCheck' => [GiiModule::className(), 'accessCheck'],
                'visible' => YII_ENV_DEV,
                'items' => [
                    'model' => [
                        'label' => 'Модель',
                        'url' => ['/gii/gii/model'],
                        'urlRule' => 'gii/model',
                    ],
                    'form-model' => [
                        'label' => 'Форма',
                        'url' => ['/gii/gii/form-model'],
                        'urlRule' => 'gii/form-model',
                    ],
                    'enum' => [
                        'label' => 'Enum',
                        'url' => ['/gii/gii/enum'],
                        'urlRule' => 'gii/enum',
                    ],
                    'crud' => [
                        'label' => 'CRUD',
                        'url' => ['/gii/gii/crud'],
                        'urlRule' => 'gii/crud',
                    ]
                ]
            ],
            'gii-new' => [
                'label' => 'Генератор кода',
                'url' => ['/gii/gii/index-new'],
                'urlRule' => 'gii/new<action:.*>',
                'order' => 500,
                'accessCheck' => [GiiModule::className(), 'accessCheck'],
                'visible' => YII_ENV_DEV,
            ],
        ];
    }

    public static function apiMap()
    {
        return [
            'api-fetch-classes' => '/api/gii/fetch-classes',
        ];
    }
    public function actionIndex()
    {
        $modules = [];
        foreach (ModelClass::findAll() as $modelClass) {
            $modules[$modelClass->moduleClass->id]['models'][] = $modelClass;
        }
        foreach (FormModelClass::findAll() as $formModelClass) {
            $modules[$formModelClass->moduleClass->id]['formModels'][] = $formModelClass;
        }
        foreach (EnumClass::findAll() as $enumClass) {
            $modules[$enumClass->moduleClass->id]['enums'][] = $enumClass;
        }
        foreach (ControllerClass::findAll() as $controllerClass) {
            $modules[$controllerClass->moduleClass->id]['cruds'][] = $controllerClass;
        }

        ksort($modules);

        return $this->render('index', [
            'modules' => $modules,
        ]);
    }

    public function actionIndexNew()
    {
        $this->layout = '@steroids/modules/gii/layouts/blank';
        return $this->renderContent(GiiApplication::widget());
    }

    public function actionApiFetchClasses()
    {
        return [
            'module' => ModuleClass::findAll(),
            'enum' => EnumClass::findAll(),
            'form' => FormModelClass::findAll(),
            'model' => ModelClass::findAll(),
            // TODO controllers
            // TODO api forms/actions
        ];
    }

    public function actionModel($moduleId = null, $modelName = null)
    {
        if (\Yii::$app->request->isPost) {
            $moduleId = \Yii::$app->request->post('moduleId');
            $modelName = \Yii::$app->request->post('modelName');

            // Check to create module
            if ($moduleId && !ModuleClass::findOne($moduleId)) {
                (new ModuleGenerator([
                    'moduleId' => $moduleId,
                ]))->generate();
            }

            // Update model
            if ($moduleId && $modelName) {
                if (\Yii::$app->request->post('refresh')) {
                    $modelClass = ModelClass::findOne(ModelClass::idToClassName($moduleId, $modelName));
                    (new ModelGenerator([
                        'oldModelClass' => $modelClass,
                        'modelClass' => $modelClass,
                        'migrateMode' => 'none',
                    ]))->generate();

                    return $this->redirect(['index']);
                } else {
                    $modelClass = new ModelClass([
                        'className' => ModelClass::idToClassName($moduleId, $modelName),
                        'tableName' => \Yii::$app->request->post('tableName'),
                    ]);
                    $modelClass->getMetaClass()->setMeta(
                        array_map(function ($item) use ($modelClass) {
                            return new MetaItem(array_merge($item, [
                                'metaClass' => $modelClass->getMetaClass(),
                            ]));
                        }, \Yii::$app->request->post('meta', []))
                    );
                    $modelClass->getMetaClass()->setRelations(
                        array_map(function ($item) {
                            $className = ArrayHelper::remove($item, 'relationModelClassName');
                            return new Relation(array_merge($item, [
                                'relationClass' => ModelClass::findOne($className),
                            ]));
                        }, \Yii::$app->request->post('relations', []))
                    );

                    (new ModelGenerator([
                        'oldModelClass' => ModelClass::findOne($modelClass->className),
                        'modelClass' => $modelClass,
                        'migrateMode' => \Yii::$app->request->post('migrateMode'),
                    ]))->generate();
                }

                return $this->redirect(['model', 'moduleId' => $moduleId, 'modelName' => $modelName]);
            }
        }

        return $this->render('model', [
            'initialValues' => [
                'moduleId' => $moduleId,
                'modelName' => $modelName,
            ],
        ]);
    }

    public function actionFormModel($moduleId = null, $formModelName = null)
    {
        if (\Yii::$app->request->isPost) {
            $moduleId = \Yii::$app->request->post('moduleId');
            $formModelName = \Yii::$app->request->post('formModelName');

            // Check to create module
            if ($moduleId && !ModuleClass::findOne($moduleId)) {
                (new ModuleGenerator([
                    'moduleId' => $moduleId,
                ]))->generate();
            }

            // Update form model
            if ($moduleId && $formModelName) {
                $formModelClass = new FormModelClass([
                    'className' => FormModelClass::idToClassName($moduleId, $formModelName),
                ]);
                $modelClass = ModelClass::findOne(\Yii::$app->request->post('modelClass') ?: $formModelClass->getModelClass());

                if (\Yii::$app->request->post('refresh')) {
                    (new FormModelGenerator([
                        'formModelClass' => $formModelClass,
                        'modelClass' => $modelClass,
                    ]))->generate();

                    return $this->redirect(['index']);
                } else {
                    $formModelClass->getMetaClass()->setMeta(
                        array_map(function ($item) use ($formModelClass) {
                            return new MetaItem(array_merge($item, [
                                'metaClass' => $formModelClass->getMetaClass(),
                            ]));
                        }, \Yii::$app->request->post('meta', []))
                    );

                    (new FormModelGenerator([
                        'formModelClass' => $formModelClass,
                        'modelClass' => $modelClass,
                    ]))->generate();
                }

                return $this->redirect(['form-model', 'moduleId' => $moduleId, 'formModelName' => $formModelName]);
            }
        }

        return $this->render('form-model', [
            'initialValues' => [
                'moduleId' => $moduleId,
                'formModelName' => $formModelName,
            ],
        ]);
    }

    public function actionEnum($moduleId = null, $enumName = null)
    {
        if (\Yii::$app->request->isPost) {
            $moduleId = \Yii::$app->request->post('moduleId');
            $enumName = \Yii::$app->request->post('enumName');

            // Check to create module
            if ($moduleId && !ModuleClass::findOne($moduleId)) {
                (new ModuleGenerator([
                    'moduleId' => $moduleId,
                ]))->generate();
            }

            // Update enum
            if ($moduleId && $enumName) {
                $enumClass = new EnumClass([
                    'className' => EnumClass::idToClassName($moduleId, $enumName),
                ]);

                if (\Yii::$app->request->post('refresh')) {
                    (new EnumGenerator([
                        'enumClass' => $enumClass,
                    ]))->generate();

                    return $this->redirect(['index']);
                } else {
                    $meta = \Yii::$app->request->post('meta', []);

                    // Inline
                    $inlineList = \Yii::$app->request->post('inlineList');
                    if ($inlineList) {
                        foreach (explode("\n", $inlineList) as $line) {
                            $line = trim($line);
                            if (!$line) {
                                continue;
                            }

                            $lineItems = explode(' ', $line);
                            if (count($lineItems) < 2) {
                                continue;
                            }
                            $key = array_shift($lineItems);
                            $label = implode(' ', $lineItems);

                            if ($key) {
                                $name = $key;
                                $name = str_replace('-', '_', $name);
                                $name = preg_replace('[^\w\d_]', '', $name);
                                if (is_numeric(substr($name, 0, 1))) {
                                    $name = 'A' . $name;
                                }

                                $meta[] = [
                                    'name' => $name,
                                    'value' => $key,
                                    'label' => $label,
                                    'cssClass' => '',
                                    'customColumns' => '',
                                ];
                            }
                        }
                    }

                    $enumClass->getMetaClass()->setMeta(
                        array_map(function ($item) use ($enumClass) {
                            if (!isset($item['value']) || $item['value'] === '') {
                                $item['value'] = $item['name'];
                            }
                            return new EnumMetaItem(array_merge($item, [
                                'metaClass' => $enumClass->getMetaClass(),
                            ]));
                        }, $meta)
                    );

                    (new EnumGenerator([
                        'enumClass' => $enumClass,
                    ]))->generate();
                }

                return $this->redirect(['enum', 'moduleId' => $moduleId, 'enumName' => $enumName]);
            }
        }

        return $this->render('enum', [
            'initialValues' => [
                'moduleId' => $moduleId,
                'enumName' => $enumName,
            ],
        ]);
    }

    public function actionCrud($moduleId = null, $controllerName = null)
    {
        if (\Yii::$app->request->isPost) {
            $moduleId = \Yii::$app->request->post('moduleId');
            $controllerName = \Yii::$app->request->post('controllerName');
            if ($controllerName && !preg_match('/Controller$/', $controllerName)) {
                $controllerName = ucfirst($controllerName) . 'Controller';
            }

            // Check to create module
            if ($moduleId && !ModuleClass::findOne($moduleId)) {
                (new ModuleGenerator([
                    'moduleId' => $moduleId,
                ]))->generate();
            }

            // Create CRUD
            if ($moduleId && $controllerName) {
                $controllerClassName = ControllerClass::idToClassName($moduleId, $controllerName);
                $controllerClass = ControllerClass::findOne($controllerClassName);
                if (!$controllerClass) {
                    $controllerClass = new ControllerClass([
                        'className' => $controllerClassName,
                    ]);
                }

                if (\Yii::$app->request->post('refresh')) {
                    (new CrudGenerator([
                        'controllerClass' => $controllerClass,
                    ]))->generate();

                    return $this->redirect(['index']);
                } else {
                    $controllerClass->getMetaClass()->setMeta(\Yii::$app->request->post('meta'));

                    (new CrudGenerator([
                        'controllerClass' => $controllerClass,
                    ]))->generate();
                }

                return $this->redirect(['crud', 'moduleId' => $moduleId, 'controllerName' => $controllerName]);
            }
        }

        return $this->render('crud', [
            'initialValues' => [
                'moduleId' => $moduleId,
                'controllerName' => $controllerName,
            ],
        ]);
    }

}
