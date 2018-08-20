<?php

namespace steroids\components;

use yii\web\Controller;

class View extends \yii\web\View
{
    /**
     * Detect overwrite library file in application views
     * @param string $view
     * @return null|string
     */
    public function findOverwriteView($view)
    {
        if (strpos($view, '@steroids/modules') === 0) {
            $appView = str_replace('@steroids/modules', '@app', $view);

            $appPath = \Yii::getAlias($appView);

            if (!pathinfo($appPath, PATHINFO_EXTENSION)) {
                $appPath = $appPath . '.php';
            }

            if (file_exists($appPath)) {
                return $appView;
            }
        }

        return $view;
    }

}