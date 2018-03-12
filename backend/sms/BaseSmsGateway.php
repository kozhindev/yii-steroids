<?php

namespace steroids\sms;

use yii\base\Component;
use yii\base\Exception;

abstract class BaseSmsGateway extends Component
{
    public $from = null;

    public $debug = false;

    /**
     * @param string $to Receiver phone number in international format "+123456789"
     * @param string $text Message contents. Support long-sms implicitly.
     * @param string|null $from Phone number or text id to select sender. Global default must be available.
     * @throws Exception
     */
    public function send($to, $text, $from = null)
    {

        if ($from === null) {
            $from = $this->from;
        }

        if ($this->debug) {
            $r = file_put_contents(
                \Yii::$app->runtimePath . '/' . str_replace('\\', '.', $this->className()) . ' ' . date('Y-m-d H-i-s ') . $to . '.txt',
                'From: ' . $from . "\n\n" . $text
            );

            if (!$r) {
                throw new Exception('Cannot save SMS.RU debug file');
            }

            return;
        }

        $this->internalSend($to, $text, $from);
    }

    /**
     * @param string $to Receiver phone number in international format "+123456789"
     * @param string $text Message contents. Support long-sms implicitly.
     * @param string $from Phone number or text id to select sender.
     * @throws Exception
     */
    abstract public function internalSend($to, $text, $from);
}
