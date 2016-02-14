<?php

namespace Perfect\Tests\Core;

use Perfect\Core\DbManager;

class DbManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        /** @var DbManager $target */
        $target = $this->getMock('Perfect\Core\DbManager', ['pdoFactory']);

        $stub = new \stdClass();
        $target->expects($this->once())
            ->method('pdoFactory')
            ->with([
                'dsn' => 'foo',
                'user' => 'bar',
                'password' => 'fuga',
                'options' => ['hoge' => 'fuga']
            ])
            ->will($this->returnValue($stub));

        $target->connect('name', [
            'dsn' => 'foo',
            'user' => 'bar',
            'password' => 'fuga',
            'options' => ['hoge' => 'fuga']
        ]);

        $this->assertSame($stub, $target->getConnection('name'));
    }
}