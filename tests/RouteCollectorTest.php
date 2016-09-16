<?php
namespace Upscale\HttpServerEngine\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use FastRoute\RouteCollector as RouteCollection;
use Upscale\HttpServerEngine\RouteCollector;

class RouteCollectorTest extends TestCase
{
    /**
     * @var RouteCollector
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new RouteCollector(__DIR__ . '/_files/routes.php');
    }

    public function testCollect($isMagicInvocation = false)
    {
        /** @var RouteCollection|MockObject $list */
        $list = $this->createMock(RouteCollection::class);
        $list->expects($this->at(0))->method('addRoute')->with('GET', '/resource', 'retrieve_resource');
        $list->expects($this->at(1))->method('addRoute')->with(['PUT', 'PATCH'], '/resource', 'update_resource');

        $subject = $this->subject;
        $isMagicInvocation
            ? $subject($list)
            : $subject->collect($list);
    }

    public function testInvoke()
    {
        $this->testCollect(true);
    }
}
