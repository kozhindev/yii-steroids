<?php

namespace steroids\notifier\providers;

use \Yii;
use yii\base\Exception;
use yii\di\Instance;
use yii\swiftmailer\Mailer;

class MailerNotifierProvider extends BaseProvider
{
    /**
     * @var Mailer
     */
    public $mailer = 'mailer';

    public function init()
    {
        parent::init();

        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
    }

    public function send($templatePath, $params, $language)
    {
        if (empty($params['email'])) {
            throw new Exception('Not found email for send mail');
        }

        // Set language for render
        $prevLanguage = Yii::$app->language;
        if ($language) {
            Yii::$app->language = $language;
        }

        // Send
        $message = $this->mailer->compose($templatePath, array_merge($params, ['user' => $this]));
        if (!$message->getSubject()) {
            $message->setSubject(Yii::$app->name);
        }
        $message->setTo($params['email'])->send();

        // Revert back lang
        Yii::$app->language = $prevLanguage;
    }
}
