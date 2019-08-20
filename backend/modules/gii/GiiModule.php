<?php

namespace steroids\modules\gii;

use steroids\base\Module;
use steroids\modules\gii\controllers\GiiController;

class GiiModule extends Module
{
    public $layout = '@steroids/modules/gii/layouts/blank';

    /**
     * @var integer the permission to be set for newly generated code files.
     * This value will be used by PHP chmod function.
     * Defaults to 0666, meaning the file is read-writable by all users.
     */
    public $newFileMode = 0666;

    /**
     * @var integer the permission to be set for newly generated directories.
     * This value will be used by PHP chmod function.
     * Defaults to 0777, meaning the directory can be read, written and executed by all users.
     */
    public $newDirMode = 0777;

    /**
     * @var array
     */
    public $allowedIPs = ['127.0.0.1', '::1'];

    public $generateJsMeta = true;

    public $showSteroidsEntries = false;

    public static function siteMap()
    {
        return [
            'gii' => [
                'label' => 'Gii',
                'accessCheck' => [GiiModule::class, 'accessCheck'],
                'items' => GiiController::coreMenuItems(),
            ],
        ];
    }

    public static function accessCheck() {
        if (!YII_ENV_DEV) {
            return false;
        }

        $ip = \Yii::$app->getRequest()->getUserIP();
        foreach (static::getInstance()->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        return false;
    }
}
