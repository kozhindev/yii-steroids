<?php

namespace steroids\modules\docs\helpers;


class TypeConverter
{
    const TYPES = [
        'primaryKey' => 'integer',
        'string' => 'string',
        'float' => 'number',
        'int' => 'integer',
        'integer' => 'integer',
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'date' => 'string',
        'null' => 'string',
    ];

    public static function find($types)
    {
        $convertedTypes = array_filter(array_map(function ($type) {
            return array_key_exists($type, static::TYPES)
                ? static::TYPES[$type]
                : null;
        }, explode('|', $types)));

        return count($convertedTypes)
            ? $convertedTypes[0]
            : null;
    }
}




