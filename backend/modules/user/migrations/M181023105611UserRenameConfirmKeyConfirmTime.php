<?php

namespace steroids\modules\user\migrations;

use steroids\base\Migration;

class M181023105611UserRenameConfirmKeyConfirmTime extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('users', 'emailConfirmKey', 'confirmKey');
        $this->renameColumn('users', 'emailConfirmTime', 'confirmTime');
    }

    public function safeDown()
    {
        $this->renameColumn('users', 'confirmKey', 'emailConfirmKey');
        $this->renameColumn('users', 'confirmTime', 'emailConfirmTime');
    }
}
