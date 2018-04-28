<?php

namespace steroids\modules\gii\forms;

interface IEntity
{
    public function load($data, $formName = null);
    public function save();
}
