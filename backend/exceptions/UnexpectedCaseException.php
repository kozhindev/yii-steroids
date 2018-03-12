<?php

namespace steroids\exceptions;

class UnexpectedCaseException extends ApplicationException
{
    public function __construct()
    {
        $message = 'Unexpected case';
        $backtrace = debug_backtrace(0, 2);
        if (isset($backtrace[1]['class'])) {
            $message .= ' in ' . $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
        }

        parent::__construct($message);
    }
}
