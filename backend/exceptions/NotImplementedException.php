<?php

namespace steroids\exceptions;

class NotImplementedException extends ApplicationException
{
    public function __construct()
    {
        $message = 'Not implemented';
        $backtrace = debug_backtrace(0, 2);
        if (isset($backtrace[1]['class'])) {
            $message .= ' yet. Called at ' . $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
        }

        parent::__construct($message);
    }
}
