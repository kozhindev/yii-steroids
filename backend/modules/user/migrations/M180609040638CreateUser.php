<?php

namespace steroids\modules\user\migrations;

use steroids\base\Migration;

class M180609040638CreateUser extends Migration
{
    public function safeUp()
    {
        if (\Yii::$app->db->schema->getTableSchema('users') === null) {
            $this->createTable('users', [
                'id' => $this->primaryKey(),
                'login' => $this->string(),
                'email' => $this->string()->notNull(),
                'name' => $this->string(),
                'phone' => $this->string(),
                'role' => $this->string(),
                'passwordHash' => $this->text(),
                'sessionKey' => $this->string(32),
                'language' => $this->string(10),
                'lastLoginIp' => $this->string(45),
                'emailConfirmKey' => $this->string(32),
                'createTime' => $this->dateTime(),
                'updateTime' => $this->dateTime(),
                'emailConfirmTime' => $this->dateTime(),
                'blockedTime' => $this->dateTime(),
                'lastLoginTime' => $this->dateTime(),
                'photoId' => $this->integer(),
            ]);
            $this->createIndex('users_email', 'users', 'email', true);
        }

    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
