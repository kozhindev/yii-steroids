<?php

namespace tests\unit;

use PHPUnit\Framework\TestCase;
use steroids\helpers\DefaultConfig;

class BootstrapTest extends TestCase
{
    public function testDefaultConfig()
    {
        $appDir = __DIR__ . '/../data/modules';
        $namespace = 'tests\\data\\modules';

        $this->assertEquals(
            [
                'one' => 'tests\data\modules\one\OneModule',
                'two' => 'tests\data\modules\two\TwoModule',
                'two.three' => 'tests\data\modules\two\three\ThreeModule',
            ],
            DefaultConfig::getModuleClasses($appDir, $namespace)
        );
        $this->assertEquals(
            ['log', 'frontendState', 'one'],
            DefaultConfig::getMainConfig(__DIR__, [], $appDir, $namespace)['bootstrap']
        );
    }
}
