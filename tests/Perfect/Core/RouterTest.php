<?php

namespace Perfect\Tests\Core;

use Perfect\Core\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $definitions = ['/item/:action/:id' => ['controller' => 'item']];
        $router = new Router($definitions);
        $result = $router->resolve('item/show/123');
        $this->assertEquals('item', $result['controller']);
        $this->assertEquals('show', $result['action']);
        $this->assertEquals('123', $result['id']);
    }

    public function testResolveUnmatch()
    {
        $definitions = ['/item/:action/:id' => ['controller' => 'item']];
        $router = new Router($definitions);
        $result = $router->resolve('foo/show/123');
        $this->assertFalse($result);
    }
}