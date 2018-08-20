<?php

namespace steroids\modules\gii\models;

use steroids\components\AuthManager;
use steroids\components\SiteMapItem;
use steroids\modules\gii\forms\ModelEntity;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

class AuthPermissionSync extends BaseObject
{
    const SEPARATOR = '::';
    const PREFIX_MODEL = 'm';
    const PREFIX_ACTION = 'a';

    static $defaultAttributeRules = [
        AuthManager::RULE_MODEL_VIEW,
        AuthManager::RULE_MODEL_CREATE,
        AuthManager::RULE_MODEL_UPDATE,
    ];
    static $readOnlyTypes = ['autoTime', 'primaryKey'];

    /**
     * Return permissions list, filtered by prefix
     * @param string $prefix
     * @return Permission[]
     */
    public static function getPermissions($prefix)
    {
        return array_filter(\Yii::$app->authManager->getPermissions(), function (Permission $permission) use ($prefix) {
            return strpos($permission->name . self::SEPARATOR, $prefix . self::SEPARATOR) === 0;
        });
    }

    /**
     * Store model and it attributes as rules
     */
    public static function syncRoles()
    {
        $auth = \Yii::$app->authManager;
        $roles = [
            AuthManager::ROLE_GUEST,
        ];

        // Find enum class
        foreach (EnumClass::findAll() as $enumClass) {
            if ($enumClass->name === 'UserRole') {
                $roles = array_merge($roles, $enumClass->className::getKeys());
            }
        }

        $prevRoles = ArrayHelper::getColumn($auth->getRoles(), 'name');
        foreach ($roles as $role) {
            if (!in_array($role, $prevRoles)) {
                $auth->add($auth->createRole($role));
            }
        }
        foreach (array_diff($prevRoles, $roles) as $role) {
            $auth->remove($auth->getRole($role));
        }
    }

    /**
     * Store model and it attributes as rules
     */
    public static function syncModels()
    {
        $prevNames = ArrayHelper::getColumn(static::getPermissions(self::PREFIX_MODEL), 'name');
        $addedNames = [];

        // Models
        foreach (ModelEntity::findAll() as $modelClass) {
            $modelPermission = self::findOrCreate([
                self::PREFIX_MODEL,
                $modelClass->className,
            ]);
            $addedNames[] = $modelPermission->name;

            // Model rules
            foreach (self::$defaultAttributeRules as $rule) {
                $attributePermission = self::findOrCreate([
                    self::PREFIX_MODEL,
                    $modelClass->className,
                    $rule,
                ]);
                $addedNames[] = $attributePermission->name;
                static::safeAddChild($modelPermission, $attributePermission);
            }
            foreach ($modelClass->getCanRules() as $rule) {
                if (!in_array($rule, self::$defaultAttributeRules)) {
                    $attributePermission = self::findOrCreate([
                        self::PREFIX_MODEL,
                        $modelClass->className,
                        $rule,
                    ]);
                    $addedNames[] = $attributePermission->name;
                    static::safeAddChild($modelPermission, $attributePermission);
                }
            }

            // Attributes
            foreach ($modelClass->attributeItems as $attributeEntity) {
                $attributePermission = self::findOrCreate([
                    self::PREFIX_MODEL,
                    $modelClass->className,
                    $attributeEntity->name,
                ]);
                $addedNames[] = $attributePermission->name;
                static::safeAddChild($modelPermission, $attributePermission);

                // Attribute rules
                foreach (self::$defaultAttributeRules as $rule) {
                    if (in_array($attributeEntity->appType, static::$readOnlyTypes)
                        && in_array($rule, [AuthManager::RULE_MODEL_CREATE, AuthManager::RULE_MODEL_UPDATE])) {
                        continue;
                    }

                    $rulePermission = self::findOrCreate([
                        self::PREFIX_MODEL,
                        $modelClass->className,
                        $attributeEntity->name,
                        $rule,
                    ]);
                    $addedNames[] = $rulePermission->name;
                    static::safeAddChild($attributePermission, $rulePermission);
                }
            }
        }

        // Remove not used permissions
        $auth = \Yii::$app->authManager;
        foreach (array_diff($prevNames, $addedNames) as $name) {
            $auth->remove($auth->getPermission($name));
        }
    }

    /**
     * Store mega menu items as rules
     */
    public static function syncActions()
    {
        $prevNames = ArrayHelper::getColumn(static::getPermissions(self::PREFIX_ACTION), 'name');

        $items = \Yii::$app->siteMap->getItems();
        static::syncActionsRecursive($items, $addedNames);

        // Remove not used permissions
        $auth = \Yii::$app->authManager;
        foreach (array_diff($prevNames, $addedNames) as $name) {
            $auth->remove($auth->getPermission($name));
        }
    }

    /**
     * @param SiteMapItem[] $items
     * @param array $addedNames
     * @param Permission $parentPermission
     * @param array $parentIds
     * @return array
     */
    protected static function syncActionsRecursive($items, &$addedNames, $parentPermission = null, $parentIds = [])
    {
        foreach ($items as $id => $item) {
            // Skip steroids gii module
            if ($parentPermission && $parentPermission->name === 'admin' && $id === 'gii') {
                continue;
            }

            $ids = array_merge($parentIds, [$id]);

            // Find or create permission
            $menuItemPermission = self::findOrCreate(array_merge(
                [self::PREFIX_ACTION],
                $ids
            ));
            $addedNames[] = $menuItemPermission->name;
            static::safeAddChild($parentPermission, $menuItemPermission);

            // Add self
            if (count($item->items) > 0 && !$item->redirectToChild) {
                $selfPermission = self::findOrCreate(array_merge(
                    [self::PREFIX_ACTION],
                    $ids,
                    [AuthManager::ACTION_SELF]
                ));
                $addedNames[] = $selfPermission->name;
                static::safeAddChild($menuItemPermission, $selfPermission);
            }

            static::syncActionsRecursive($item->items, $addedNames, $menuItemPermission, $ids);
        }
    }

    /**
     * @param Permission $parent
     * @param Permission $child
     */
    public static function safeAddChild($parent, $child)
    {
        $auth = \Yii::$app->authManager;
        if ($parent && $auth->canAddChild($parent, $child)) {
            $existsChildNames = ArrayHelper::getColumn($auth->getChildren($parent->name), 'name');
            if (!in_array($child->name, $existsChildNames)) {
                $auth->addChild($parent, $child);
            }
        }
    }

    /**
     * @param array $path
     * @return Permission
     */
    protected static function findOrCreate(array $path)
    {
        $auth = \Yii::$app->authManager;
        $name = implode(self::SEPARATOR, $path);

        $permission = $auth->getPermission($name);
        if (!$permission) {
            $permission = $auth->createPermission($name);
            $permission->description = end($path);
            $auth->add($permission);
        }
        return $permission;
    }

}