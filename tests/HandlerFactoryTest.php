<?php
namespace Upscale\HttpServerEngine\Tests;

use Aura\Di\Container;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use FastRoute\Dispatcher;
use Upscale\HttpServerEngine\HandlerFactory;
use Upscale\HttpServerEngine\HandlerInterface;

class HandlerFactoryTest extends TestCase
{
    /**
     * @var HandlerFactory
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $di;

    protected function setUp()
    {
        $this->di = $this->createMock(Container::class);

        $this->subject = new HandlerFactory($this->di);
    }

    /**
     * @param string $class
     * @param array $args
     * @param object $expectedResult
     * @dataProvider createDataProvider
     */
    public function testCreate($class, array $args, $expectedResult)
    {
        $this->di
            ->expects($this->once())
            ->method('newInstance')
            ->with($class, $args)
            ->willReturn($expectedResult);

        $actualResult = $this->subject->create($class, $args);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function createDataProvider()
    {
        return [
            [
                'FixtureActionClass',
                ['param1' => 'value1', 'param2' => 'value2'],
                $this->createMock(HandlerInterface::class),
            ],
        ];
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Class "stdClass" has to implement "Upscale\HttpServerEngine\HandlerInterface"
     */
    public function testCreateException()
    {
        $this->testCreate('stdClass', [], new \stdClass());
    }
}
