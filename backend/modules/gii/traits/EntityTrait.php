<?php

namespace steroids\modules\gii\traits;

trait EntityTrait
{
    public function getNamespace()
    {
        return substr($this->getClassName(), 0, -1 * (strlen($this->name) + 1));
    }
}