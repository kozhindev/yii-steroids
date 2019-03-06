<?php

namespace steroids\base;

class BaseSchema extends FormModel
{
    /**
     * @var FormModel|Model
     */
    public $model;

    /**
     * @param $models
     * @return static[]
     */
    public static function toList($models)
    {
        return array_map(function($model) {
            return new static(['model' => $model]);
        }, $models ?: []);
    }
}
