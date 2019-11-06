<?php

namespace steroids\exceptions;

use steroids\components\SentryTarget;
use Throwable;
use yii\base\Exception;

/**
 * Class SentryException
 * @package steroids\exceptions
 *
 * This is an additional exception type, which should be used with enhanced SentryTarget component.
 * @see SentryTarget
 */
class SentryException extends Exception {
    public $uid;

    public function __construct($message = "", $uid = null, Throwable $previous = null, $code = 0)
    {
        $this->uid = $uid;

        parent::__construct($message, $code, $previous);
    }
}