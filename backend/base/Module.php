<?php

namespace steroids\base;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * @package steroids\base
 */
class Module extends \yii\base\Module
{
    /**
     * @return static
     * @throws Exception
     */
    public static function getInstance()
    {
        if (!preg_match('/([^\\\]+)Module$/', static::className(), $match)) {
            throw new Exception('Cannot auto get module id by class name: ' . static::className());
        }

        /** @var Module $module */
        $module = \Yii::$app->getModule(lcfirst($match[1]));
        return $module;
    }

    /**
     * Part of site menu for this module
     * @return array
     */
    public static function siteMap()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Layout for admin modules (as submodule in application modules)
        if ($this->id === 'admin') {
            $this->layout = '@app/core/admin/layouts/web';
        }

        parent::init();

        $this->initCoreComponents();
    }

    protected function initCoreComponents()
    {
        $coreComponents = $this->coreComponents();
        foreach ($coreComponents as $id => $config) {
            if (is_string($this->$id)) {
                $config = ['class' => $this->$id];
            } elseif (is_array($this->$id)) {
                $config = ArrayHelper::merge($config, $this->$id);
            }
            $this->$id = \Yii::createObject($config);
        }
    }

    protected function coreComponents()
    {
        return [];
    }

}