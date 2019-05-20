<?php

namespace extpoint\yii2\file\migrations;

use \extpoint\yii2\base\Migration;

class M180719150406FileAwsS3 extends Migration
{
    public function up()
    {
        $this->addColumn('{{%files}}', 'sourceType', $this->string('32'));
        $this->addColumn('{{%files}}', 'amazoneS3Url', $this->text());
        $this->addColumn('{{%files_images_meta}}', 'amazoneS3Url', $this->text());
    }

    public function down()
    {
        $this->dropColumn('{{%files}}', 'sourceType');
        $this->dropColumn('{{%files}}', 'amazoneS3Url');
        $this->dropColumn('{{%files_images_meta}}', 'amazoneS3Url');
    }

}
