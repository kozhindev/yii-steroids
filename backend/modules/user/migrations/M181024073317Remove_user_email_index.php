<?php

namespace steroids\modules\user\migrations;

use steroids\base\Migration;

class M181024073317Remove_user_email_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('users_email', 'users');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('users_email', 'users', 'email', true);
    }
}
