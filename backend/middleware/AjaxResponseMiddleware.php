<?php

namespace steroids\middleware;

use steroids\base\FormModel;
use steroids\base\SearchModel;
use steroids\widgets\ActiveForm;
use yii\base\ActionEvent;
use yii\base\BaseObject;
use yii\data\BaseDataProvider;
use yii\web\Application;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class AjaxResponseMiddleware extends BaseObject
{
    /**
     * @param Application $app
     */
    public static function register($app)
    {
        if ($app instanceof Application) {
            $app->on(Controller::EVENT_AFTER_ACTION, [static::className(), 'checkAjaxResponse']);
        }
    }

    /**
     * @param ActionEvent $event
     * @throws ForbiddenHttpException
     */
    public static function checkAjaxResponse($event)
    {
        $request = \Yii::$app->request;
        $response = \Yii::$app->response;

        $rawContentType = $request->contentType;
        if (($pos = strpos($rawContentType, ';')) !== false) {
            // e.g. application/json; charset=UTF-8
            $contentType = substr($rawContentType, 0, $pos);
        } else {
            $contentType = $rawContentType;
        }

        if (($contentType === 'application/json' && isset($request->parsers[$contentType])) || $response->format === Response::FORMAT_JSON) {

            // Detect data provider
            if ($event->result instanceof SearchModel || $event->result instanceof Model) {
                $data = $event->result->toFrontend();
            } elseif ($event->result instanceof FormModel) {
                $data = ActiveForm::renderAjax($event->result, '');
            } elseif ($event->result instanceof BaseDataProvider) {
                $data = [
                    'meta' => null,
                    'items' => $event->result->models,
                    'total' => $event->result->totalCount,
                ];
            } else {
                $data = is_array($event->result) ? $event->result : [];
            }

            // Ajax redirect
            $location = $response->headers->get('Location')
                ?: $response->headers->get('X-Pjax-Url')
                    ?: $response->headers->get('X-Redirect');
            if ($location) {
                $data['redirectUrl'] = $location;
                $response->headers->remove('Location');
                $response->statusCode = 200;
            } else {
                // Flashes
                $session = \Yii::$app->session;
                $flashes = $session->getAllFlashes(true);
                if (!empty($flashes)) {
                    $data['flashes'] = $flashes;
                }
            }

            $event->result = $data;
        }

        if (is_array($event->result)) {
            $response->format = Response::FORMAT_JSON;
        }
    }
}
