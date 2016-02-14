<?php
namespace Perfect\Tests\Core;

use Perfect\Core\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function testGetBaseUrl()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $request = new Request();
        $this->assertEquals('', $request->getBaseUrl());
    }

    public function testGetBaseUrlWithIndex()
    {
        $_SERVER['REQUEST_URI'] = '/index.php/foo';
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $request = new Request();
        $this->assertEquals('/index.php', $request->getBaseUrl());
    }

    public function testGetBaseUrlHasHierarchy()
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/list';
        $_SERVER['SCRIPT_NAME'] = '/foo/bar/index.php';

        $request = new Request();
        $this->assertEquals('/foo/bar', $request->getBaseUrl());
    }

    public function testGetPathInfo()
    {
        $_SERVER['REQUEST_URI'] = '/list?foo=bar';
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $request = new Request();
        $this->assertEquals('/list', $request->getPathInfo());
    }
}