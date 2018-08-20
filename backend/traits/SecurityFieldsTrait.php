<?php

namespace steroids\traits;


trait SecurityFieldsTrait
{
    protected $securityFields = [];

    public function getSecurityFields()
    {
        return $this->securityFields;
    }

    public function hasSecurityFields()
    {
        return !empty($this->getSecurityFields());
    }

    public function addSecurityFields(array $fields)
    {
        $this->securityFields[] = $fields;
    }
}