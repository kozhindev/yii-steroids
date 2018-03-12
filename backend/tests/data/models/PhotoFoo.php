<?php

namespace tests\data\models;

use steroids\base\Model;

/**
 * @property integer $id
 * @property integer $photoId
 * @property string $name
 */
class PhotoFoo extends Model
{
    public static function tableName()
    {
        return 'test_photo_foo';
    }

    public function rules()
    {
        return [
            ['photoId', 'safe'],
            ['name', 'string'],
        ];
    }
}
