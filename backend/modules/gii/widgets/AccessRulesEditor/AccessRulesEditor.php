<?php

namespace steroids\modules\gii\widgets\AccessRulesEditor;

use steroids\base\Widget;
use steroids\modules\gii\controllers\AccessController;
use steroids\modules\gii\models\AuthPermissionSync;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

class AccessRulesEditor extends Widget
{
    public $prefix;
    public $enableInlineMode;

    public function init()
    {
        $auth = \Yii::$app->authManager;

        // Get permissions and roles
        $permissions = AuthPermissionSync::getPermissions($this->prefix);
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
        $initialValues = [];
        foreach ($roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                $initialValues['rules'][$role][$permission->name] = true;
                foreach ($this->getChildNamesRecursive($permission->name) as $child) {
                    $initialValues['rules'][$role][$child->name] = true;
                };
            }
        }

        echo $this->renderReact([
            'csrfToken' => \Yii::$app->request->csrfToken,
            'roles' => $roles,
            'enableInlineMode' => $this->enableInlineMode,
            'permissions' => array_map(function (Permission $permission) use ($auth) {
                $children = array_values(ArrayHelper::getColumn($auth->getChildren($permission->name), 'name'));
                return [
                    'name' => $permission->name,
                    'description' => (string) $permission->description,
                    'children' => !empty($children) ? $children : null,
                ];
            }, array_values($permissions)),
            'initialValues' => !empty($initialValues) ? $initialValues : null,
        ]);
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