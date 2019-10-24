<?php

namespace steroids\migrations;

use steroids\base\Migration;

class M000000000002Auth extends Migration
{
    public $usersTable = 'users';

    public function safeUp()
    {
        $this->createTable('auth_confirms', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'email' => $this->string(),
            'code' => $this->string(32),
            'isConfirmed' => $this->boolean()->notNull()->defaultValue(0),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'expireTime' => $this->dateTime(),
        ]);
        $this->createForeignKey('auth_confirms', 'userId', $this->usersTable, 'id');

        $this->createTable('auth_socials', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(36),
            'userId' => $this->integer(),
            'externalId' => $this->string(),
            'socialName' => $this->string(),
            'profileJson' => $this->text(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createForeignKey('auth_socials', 'userId', $this->usersTable, 'id');

        $this->createTable('auth_logins', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'authId' => $this->integer(),
            'accessToken' => $this->string(64),
            'wsToken' => $this->string(16),
            'ipAddress' => $this->string(64),
            'location' => $this->string(),
            'userAgent' => $this->string(),
            'createTime' => $this->dateTime(),
            'expireTime' => $this->dateTime(),
        ]);
        $this->createForeignKey('auth_logins', 'userId', $this->usersTable, 'id');
    }

    public function safeDown()
    {
        $this->deleteForeignKey('auth_logins', 'userId', $this->usersTable, 'id');
        $this->dropTable('auth_logins');

        $this->deleteForeignKey('auth_socials', 'userId', $this->usersTable, 'id');
        $this->dropTable('auth_socials');

        $this->deleteForeignKey('auth_confirms', 'userId', $this->usersTable, 'id');
        $this->dropTable('auth_confirms');
    }
}
