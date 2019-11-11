<?php

namespace steroids\migrations;

use steroids\base\Migration;

class M000000000001User extends Migration
{
    public $usersTable = 'users';

    public function safeUp()
    {
        $this->createTable($this->usersTable, [
            'id' => $this->primaryKey(),
            'role' => $this->string(),
            'username' => $this->string(),
            'email' => $this->string(),
            'phone' => $this->string(20),
            'passwordHash' => $this->text(),
            'language' => $this->string(10),
            'isBanned' => $this->boolean()->notNull()->defaultValue(0),
            'isUnSubscribed' => $this->boolean()->notNull()->defaultValue(0),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->usersTable);
    }
}
