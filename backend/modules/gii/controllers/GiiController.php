<?php

namespace steroids\modules\gii\controllers;

use steroids\base\Type;
use steroids\modules\gii\enums\ClassType;
use steroids\modules\gii\forms\CrudEntity;
use steroids\modules\gii\forms\EnumEntity;
use steroids\modules\gii\forms\FormEntity;
use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\forms\WidgetEntity;
use steroids\modules\gii\GiiModule;
use steroids\modules\gii\helpers\GiiHelper;
use steroids\modules\gii\models\AuthPermissionSync;
use steroids\modules\gii\models\MetaItem;
use steroids\modules\gii\widgets\GiiApplication\GiiApplication;
use steroids\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class GiiController extends Controller
{
    public static function coreMenuItems()
    {
        return [
            'gii' => [
                'label' => 'Новый',
                'url' => ['/gii/gii/index'],
                'urlRule' => 'gii',
                'order' => 500,
                'accessCheck' => [GiiModule::class, 'accessCheck'],
                'visible' => YII_ENV_DEV,
                'items' => [
                    'gii-children' => [
                        'label' => 'Новый',
                        'url' => ['/gii/gii/index'],
                        'urlRule' => 'gii/<action:.+>',
                        'order' => 500,
                        'accessCheck' => [GiiModule::class, 'accessCheck'],
                        'visible' => YII_ENV_DEV,
                    ],
                ]
            ],
        ];
    }

    public static function apiMap()
    {
        return [
            'api-gii' => [
                'visible' => false,
                'items' => [
                    'api-get-entities' => '/api/gii/get-entities',
                    'api-class-save' => '/api/gii/class-save',
                    'api-get-permissions' => '/api/gii/get-permissions',
                    'api-permissions-save' => '/api/gii/permissions-save',
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        AuthPermissionSync::syncActions();

        return $this->renderContent(GiiApplication::widget());
    }

    public function actionApiGetEntities()
    {
        \Yii::$app->language = \Yii::$app->sourceLanguage;

        $moduleIds = array_keys(GiiHelper::findModules());
        sort($moduleIds);

        return [
            'moduleIds' => $moduleIds,
            'classes' => [
                'crud' => CrudEntity::findAll(),
                'enum' => EnumEntity::findAll(),
                'form' => FormEntity::findAll(),
                'model' => ModelEntity::findAll(),
                'widget' => WidgetEntity::findAll(),
            ],
            'appTypes' => array_map(function ($appType) {
                /** @type Type $appType */
                $additionalFields = $appType->giiOptions();
                return [
                    'name' => $appType->name,
                    'title' => ucfirst($appType->name),
                    'additionalFields' => !empty($additionalFields) ? $additionalFields : null,
                ];
            }, \Yii::$app->types->getTypes()),
        ];
    }

    public function actionApiGetPermissions()
    {
        AuthPermissionSync::syncModels();

        $auth = \Yii::$app->authManager;
        $prefix = \Yii::$app->request->post('prefix');

        // Get permissions and roles
        $permissions = AuthPermissionSync::getPermissions($prefix);
        $roles = array_values(ArrayHelper::getColumn($auth->getRoles(), 'name'));
        usort($roles, function($a, $b) {
            if ($a === 'admin' || $b === 'guest') {
                return 1;
            }
            if ($a === 'guest' || $b === 'admin') {
                return -1;
            }
            return 0;
        });

        // Initial values
        $initialValues = [
            'prefix' => $prefix,
        ];
        foreach ($roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                $initialValues['rules'][$role][$permission->name] = true;
                foreach ($this->getChildNamesRecursive($permission->name) as $child) {
                    $initialValues['rules'][$role][$child->name] = true;
                };
            }
        }

        return [
            'roles' => $roles,
            'permissions' => array_map(function (Permission $permission) use ($auth) {
                $children = array_values(ArrayHelper::getColumn($auth->getChildren($permission->name), 'name'));
                return [
                    'name' => $permission->name,
                    'description' => (string) $permission->description,
                    'children' => !empty($children) ? $children : null,
                ];
            }, array_values($permissions)),
            'initialValues' => $initialValues,
        ];
    }

    public function actionApiPermissionsSave()
    {
        $prefix = \Yii::$app->request->post('prefix');
        $data = \Yii::$app->request->post('rules');
        $allNames = ArrayHelper::getColumn(AuthPermissionSync::getPermissions($prefix), 'name');

        $auth = \Yii::$app->authManager;
        foreach ($auth->getRoles() as $role) {
            $rules = ArrayHelper::getValue($data, $role->name, []);
            $addedNames = [];
            $prevNames = ArrayHelper::getColumn($auth->getPermissionsByRole($role->name), 'name');
            $prevNames = array_filter($prevNames, function($name) use ($allNames) {
                return in_array($name, $allNames);
            });

            foreach ($rules as $rule => $bool) {
                if (!$bool || strpos($rule, $prefix . '::') !== 0) {
                    continue;
                }

                // Find parent permission and check checked
                $isParentChecked = false;
                foreach ($allNames as $permissionName) {
                    $childNames = ArrayHelper::getColumn($auth->getChildren($permissionName), 'name');
                    if (in_array($rule, $childNames) && ArrayHelper::getValue($rules, $permissionName)) {
                        $isParentChecked = true;
                        break;
                    }
                }

                if (!$isParentChecked) {
                    $addedNames[] = $rule;
                    $permission = $auth->getPermission($rule);

                    AuthPermissionSync::safeAddChild($role, $permission);
                }
            }

            // Remove unchecked
            foreach (array_diff($prevNames, $addedNames) as $name) {
                $auth->removeChild($role, $auth->getPermission($name));
            }
        }

        \Yii::$app->session->addFlash('success', 'Permissions ' . $prefix . '::* updated');
    }

    public function actionApiClassSave()
    {
        switch (\Yii::$app->request->post('classType')) {
            case ClassType::MODEL:
                $entity = new ModelEntity();
                $entity->listenRelationData('attributeItems');
                $entity->listenRelationData('relationItems');
                break;

            case ClassType::FORM:
                $entity = new FormEntity();
                $entity->listenRelationData('attributeItems');
                $entity->listenRelationData('relationItems');
                break;

            case ClassType::CRUD:
                $entity = new CrudEntity();
                $entity->listenRelationData('items');
                break;

            case ClassType::ENUM:
                $entity = new EnumEntity();
                $entity->listenRelationData('items');
                break;

            case ClassType::WIDGET:
                $entity = new WidgetEntity();
                break;

            default:
                throw new BadRequestHttpException();
        }

        if ($entity->load(\Yii::$app->request->post())) {
            $entity->save();
        }

        return ActiveForm::renderAjax($entity);
    }

    protected function getChildNamesRecursive($permissionName)
    {
        $auth = \Yii::$app->authManager;
        $auth->getPermission($permissionName);
        $names = [];
        foreach ($auth->getChildren($permissionName) as $child) {
            $names[] = $child;
            $names = array_merge($names, $this->getChildNamesRecursive($child->name));
        }
        return $names;
    }

}
