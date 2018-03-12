<?php

namespace tests\data\models;

use steroids\base\Model;

/**
 * @property integer $id
 * @property string $name
 */
class PhotoBar extends Model
{
    public static function tableName()
    {
        return 'test_photo_bar';
    }

    public function rules()
    {
        return [
            ['name', 'string'],
        ];
    }
}
