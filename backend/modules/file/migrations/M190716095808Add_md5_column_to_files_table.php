<?php

namespace steroids\modules\file\migrations;

use steroids\base\Migration;

/**
 * Handles adding md5 to table `{{%files}}`.
 */
class M190716095808Add_md5_column_to_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('files', 'md5',  $this->string());
        $this->addColumn('files', 'userId',  $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('files', 'md5');
        $this->dropColumn('files', 'userId');
    }
}
