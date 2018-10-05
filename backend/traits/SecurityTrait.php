<?php

namespace steroids\traits;


trait SecurityTrait
{
    protected $securityComponent;

    public function getSecurityComponent()
    {
        return $this->securityComponent;
    }

    public function isSecurityRequired()
    {
        return $this->securityComponent !== null;
    }

    public function requireSecurityComponent($value)
    {
        $this->securityComponent = $value;
    }
}