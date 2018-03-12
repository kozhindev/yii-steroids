<?php

namespace steroids\components;

use steroids\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\rbac\PhpManager;

class AuthManager extends PhpManager
{
    const ROLE_GUEST = 'guest';
    const RULE_SEPARATOR = '::';
    const RULE_PREFIX_MODEL = 'm';
    const RULE_PREFIX_ACTION = 'a';
    const ACTION_SELF = 'self';
    const RULE_MODEL_VIEW = 'view';
    const RULE_MODEL_CREATE = 'create';
    const RULE_MODEL_UPDATE = 'update';
    const RULE_MODEL_DELETE = 'delete';

    public $itemFile = '@app/config/rbac/items.php';
    public $assignmentFile = '@app/config/rbac/assignments.php';
    public $ruleFile = '@app/config/rbac/rules.php';

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $rule
     * @return bool
     */
    public function checkModelAccess($user, $model, $rule)
    {
        if ($this->checkModelAccessInternal($user, $model, $rule)) {
            return true;
        }

        if (is_object($model) && $model instanceof \yii\base\Model) {
            foreach ($model->attributes() as $attribute) {
                if ($this->checkAttributeAccessInternal($user, $model, $attribute, $rule)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $attribute
     * @param string $rule
     * @return bool
     */
    public function checkAttributeAccess($user, $model, $attribute, $rule)
    {
        return $this->checkAttributeAccessInternal($user, $model, $attribute, $rule)
            || $this->checkModelAccessInternal($user, $model, $rule);
    }

    /**
     * @param $user
     * @param SiteMapItem $item
     * @return bool
     */
    public function checkMenuAccess($user, $item)
    {
        $permissionName = implode(self::RULE_SEPARATOR, array_merge(
            [self::RULE_PREFIX_ACTION],
            $item->pathIds,
            count($item->items) > 0 && !$item->redirectToChild ? [self::ACTION_SELF] : []
        ));
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if ($userId === null) {
            return [
                self::ROLE_GUEST => new Assignment([
                    'roleName' => self::ROLE_GUEST,
                ]),
            ];
        }

        /** @var Model $userClass */
        $userClass = \Yii::$app->user->identityClass;
        $user = $userClass::findOne($userId);
        if (!$user) {
            return [];
        }

        return [
            $user->role => new Assignment([
                'userId' => $userId,
                'roleName' => $user->role,
            ]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        $assignments = $this->getAssignments($userId);
        return ArrayHelper::getValue($assignments, '0.roleName') === $roleName ? $assignments[0] : null;
    }

    /**
     * @inheritdoc
     */
    public function getUserIdsByRole($roleName)
    {
        /** @var Model $userClass */
        $userClass = \Yii::$app->user->identityClass;
        return $userClass::find()
            ->select('id')
            ->where([
                'role' => $roleName,
            ])
            ->column();
    }

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $rule
     * @return bool
     */
    protected function checkModelAccessInternal($user, $model, $rule)
    {
        if ($model instanceof BaseObject) {
            $model = $model::className();
        }

        $permissionName = implode(self::RULE_SEPARATOR, [
            self::RULE_PREFIX_MODEL,
            $model,
            $rule,
        ]);
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $attribute
     * @param string $rule
     * @return bool
     */
    protected function checkAttributeAccessInternal($user, $model, $attribute, $rule)
    {
        if ($model instanceof BaseObject) {
            $model = $model::className();
        }

        $permissionName = implode(self::RULE_SEPARATOR, [
            self::RULE_PREFIX_MODEL,
            $model,
            $attribute,
            $rule,
        ]);
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }
}