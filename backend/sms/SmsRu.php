<?php

namespace steroids\sms;

use yii\base\Exception;

class SmsRu extends BaseSmsGateway
{
    /** @var string */
    public $apiId;

    /** @var array|null */
    public $lastResult;

    /**
     * @param string $to
     * @param string $text
     * @param string [$from]
     * @throws Exception
     */
    public function internalSend($to, $text, $from = null)
    {
        $ch = curl_init("http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $post = [
            "api_id" => $this->apiId,
            "to" => $to,
            "text" => $text,
        ];
        // check from
        if ($from) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $from) || preg_match('/^[0-9]+$/', $from)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $from;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $this->lastResult = curl_exec($ch);
        curl_close($ch);

        // Success path
        if (is_string($this->lastResult)) {
            $this->lastResult = explode("\n", $this->lastResult);

            if ($this->lastResult[0] == 100) {
                return; // OK
            }
        }

        // Failure
        ob_start();
        var_dump($this->lastResult);
        $this->lastResult = ob_get_clean();

        throw new Exception('SMS.RU request failed: ' . $this->lastResult);
    }

}
