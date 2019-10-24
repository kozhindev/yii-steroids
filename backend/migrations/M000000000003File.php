<?php

namespace steroids\migrations;

use steroids\base\Migration;

class M000000000003File extends Migration
{
    public $usersTable = 'users';

    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(36),
            'title' => $this->string(),
            'folder' => $this->string(),
            'fileName' => $this->string(),
            'fileMimeType' => $this->string(),
            'fileSize' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'isTemp' => $this->boolean(),
            'sourceType' => $this->string('32'),
            'amazoneS3Url' => $this->text(),
            'md5' => $this->string(),
            'userId' => $this->integer(),
        ]);
        $this->createIndex('uid', 'files', 'uid');
        $this->createForeignKey('files', 'userId', $this->usersTable, 'id');

        $this->createTable('files_images_meta', [
            'id' => $this->primaryKey(),
            'fileId' => $this->integer(),
            'folder' => $this->string(),
            'fileName' => $this->string(),
            'fileMimeType' => $this->string(),
            'isOriginal' => $this->boolean(),
            'width' => $this->smallInteger(),
            'height' => $this->smallInteger(),
            'processor' => $this->string(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'amazoneS3Url' => $this->text(),
        ]);
        $this->createIndex('file_processor', 'files_images_meta', [
            'fileId',
            'processor',
        ]);
        $this->createIndex('original', 'files_images_meta', [
            'fileId',
            'isOriginal',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('files');
        $this->dropTable('files_images_meta');
    }
}
