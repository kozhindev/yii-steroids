<?php

namespace steroids\components;

use yii\web\Controller;

class View extends \yii\web\View
{
    /**
     * Detect overwrite library file in application views
     * @param string $view
     * @param null $context
     * @return null|string
     */
    public static function findOverwriteView($view, $context = null)
    {
        $appView = null;
        if (preg_match('/^[^@\\/]+/$', $view) && $context && $context instanceof Controller) {
            $appView = $context->viewPath . '/' . $view;
        }
        if (strpos($view, '@steroids/modules') === 0) {
            $appView = \Yii::getAlias(str_replace('@steroids/modules', '@app', $view));
        }

        if ($appView) {
            if (!pathinfo($appView, PATHINFO_EXTENSION)) {
                $appView = $appView . '.php';
            }
            if (file_exists($appView)) {
                return $appView;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function findViewFile($view, $context = null)
    {
        if ($appView = static::findOverwriteView($view, $context)) {
            $view = $appView;
        }

        return parent::findViewFile($view, $context);
    }
}