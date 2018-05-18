<?php

namespace steroids\middleware;

use yii\base\ActionEvent;
use yii\base\BaseObject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AccessMiddleware extends BaseObject
{
    /**
     * @param Application $app
     */
    public static function register($app)
    {
        if ($app instanceof Application) {
            $app->on(Controller::EVENT_BEFORE_ACTION, [static::className(), 'checkAccess']);
        }
    }

    /**
     * @param ActionEvent $event
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ForbiddenHttpException
     */
    public static function checkAccess($event)
    {
        // Skip gii and debug modules
        if (in_array($event->action->controller->module->id, ['debug', 'gii'])) {
            return;
        }

        // Skip error action
        if ($event->action->uniqueId === \Yii::$app->errorHandler->errorAction) {
            return;
        }

        $item = \Yii::$app->siteMap->getActiveItem();
        if (!$item) {
            throw new NotFoundHttpException();
        }
        if (!$item->checkVisible($item->normalizedUrl)) {
            if (\Yii::$app->user->isGuest) {
                \Yii::$app->user->loginRequired();
            } else {
                $messages = \Yii::t('app', 'Нет доступа');
                if (YII_ENV_DEV) {
                    $messages .= '. ' . \Yii::t('app', 'Возможно не заданы права доступа?');
                    $messages .= ' - ' . Url::to(['/gii/access/actions'], true);
                }
                throw new ForbiddenHttpException($messages);
            }
            // TODO Show 403?
            //\Yii::$app->response->redirect(\Yii::$app->homeUrl);
            $event->isValid = false;
        }
    }
}
