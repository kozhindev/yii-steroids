<?php

namespace steroids\modules\user\migrations;

use steroids\base\Migration;

class M181031073318UserConfirmKey extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('users', 'confirmKey', 'emailConfirmKey');
        $this->renameColumn('users', 'confirmTime', 'emailConfirmTime');
        $this->addColumn('users', 'phoneConfirmKey', $this->string(10));
        $this->addColumn('users', 'phoneConfirmTime', $this->dateTime());
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'phoneConfirmKey');
        $this->dropColumn('users', 'phoneConfirmTime');
        $this->renameColumn('users', 'emailConfirmKey', 'confirmKey');
        $this->renameColumn('users', 'emailConfirmTime', 'confirmTime');
    }
}
