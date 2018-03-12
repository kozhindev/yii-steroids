<?php

namespace tests\data\models;

use steroids\base\Model;

/**
 * @property integer $id
 * @property integer $articleId
 * @property string $fileName
 */
class Attachment extends Model
{
    public static function tableName()
    {
        return 'test_attachments';
    }

    public function rules()
    {
        return [
            ['articleId', 'safe'],
            ['fileName', 'string'],
        ];
    }
}
