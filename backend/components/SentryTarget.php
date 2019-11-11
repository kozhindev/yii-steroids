<?php
namespace steroids\components;

use steroids\exceptions\SentryException;
use yii\helpers\ArrayHelper;

/**
 * This is enhanced SentryTarget, which contains a handler for a additional type of exceptions - SentryException.
 * SentryException allow you to set event id for an entry which is being logged to Sentry.
 * Then you can get detailed error info in Sentry by this id and use the id on your app server whatever you like.
 *
 * @see SentryException
 */
class SentryTarget extends \notamedia\sentry\SentryTarget
{
    public function export()
    {
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp, $traces) = $message;

            $data = [
                'level' => static::getLevelName($level),
                'timestamp' => $timestamp,
                'tags' => ['category' => $category]
            ];

            if ($text instanceof \Throwable || $text instanceof \Exception) {
                if ($text instanceof SentryException && $text->uid) {
                    $data['event_id'] = $text->uid;
                }

                $this->client->captureException($text, $data);
                return;
            } elseif (is_array($text)) {
                if (isset($text['msg'])) {
                    $data['message'] = $text['msg'];
                    unset($text['msg']);
                }

                if (isset($text['tags'])) {
                    $data['tags'] = ArrayHelper::merge($data['tags'], $text['tags']);
                    unset($text['tags']);
                }

                $data['extra'] = $text;
            } else {
                $data['message'] = $text;
            }

            if ($this->context) {
                $data['extra']['context'] = parent::getContextMessage();
            }

            if (is_callable($this->extraCallback) && isset($data['extra'])) {
                $data['extra'] = call_user_func($this->extraCallback, $text, $data['extra']);
            }

            $this->client->capture($data, $traces);
        }
    }
}
