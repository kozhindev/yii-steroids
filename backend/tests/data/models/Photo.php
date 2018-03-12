<?php

namespace tests\data\models;

use steroids\base\Model;

/**
 * @property integer $id
 * @property string $barId
 * @property string $fileName
 * @property PhotoBar $bar
 * @property PhotoFoo $foo
 */
class Photo extends Model
{
    public static function tableName()
    {
        return 'test_photos';
    }

    public function rules()
    {
        return [
            ['barId', 'safe'],
            ['fileName', 'string'],
        ];
    }

    public function getBar()
    {
        return $this->hasOne(PhotoBar::className(), ['id' => 'barId']);
    }

    public function getFoo()
    {
        return $this->hasOne(PhotoFoo::className(), ['photoId' => 'id']);
    }
}
