<?php
namespace Upscale\HttpServerEngine\Tests;

use Aura\Di\Container;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Upscale\HttpServerEngine\ActionFactory;
use Upscale\HttpServerEngine\ActionInterface;
use Upscale\HttpServerEngine\FrontController;

class ActionFactoryTest extends TestCase
{
    /**
     * @var ActionFactory
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $di;

    protected function setUp()
    {
        $this->di = $this->createMock(Container::class);

        $this->subject = new ActionFactory($this->di);
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
                $this->createMock(ActionInterface::class),
            ],
        ];
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Class "stdClass" has to implement "Upscale\HttpServerEngine\ActionInterface"
     */
    public function testCreateException()
    {
        $this->testCreate('stdClass', [], new \stdClass());
    }
}
