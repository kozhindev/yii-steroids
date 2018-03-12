<?php

namespace tests\data\models;

use steroids\base\Model;
use yii\db\ActiveQuery;

/**
 * @property integer $id
 * @property integer $categoryId
 * @property string $title
 * @property Category $category
 * @property Attachment[] $attachments
 * @property Photo[] $photos
 * @property Attachment $file
 */
class Article extends Model
{
    public $photosIds;
    public $attachmentsIds;

    public static function tableName()
    {
        return 'test_article';
    }

    public function rules()
    {
        return [
            [['categoryId', 'photoIds', 'attachmentsIds'], 'safe'],
            ['title', 'string'],
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryId']);
    }

    public function getFile()
    {
        return $this->hasOne(Attachment::className(), ['articleId' => 'id']);
    }

    public function getAttachments()
    {
        return $this->hasMany(Attachment::className(), ['articleId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['id' => 'articleId'])
            ->viaTable('test_article_photos', ['photoId' => 'id']);
    }
}
