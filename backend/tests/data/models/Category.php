<?php

namespace tests\data\models;

use steroids\base\Model;

/**
 * @property integer $id
 * @property string $title
 * @property Article[] $articles
 */
class Category extends Model
{
    public static function tableName()
    {
        return 'test_category';
    }

    public function rules()
    {
        return [
            ['title', 'string'],
        ];
    }

    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['categoryId' => 'id']);
    }
}
