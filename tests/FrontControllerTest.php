<?php
namespace Upscale\HttpServerEngine\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Aura\Di\Container;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Upscale\HttpServerEngine\ActionInterface;
use Upscale\HttpServerEngine\FrontController;

class FrontControllerTest extends TestCase
{
    /**
     * @var FrontController
     */
    private $subject;

    /**
     * @var Dispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Container|MockObject
     */
    private $di;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    /**
     * @var ActionInterface|MockObject
     */
    private $handler;

    protected function setUp()
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('getPath')->willReturn('/resource');

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->request->expects($this->once())->method('getMethod')->willReturn('TEST');
        $this->request->expects($this->once())->method('getUri')->willReturn($uri);

        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->handler = $this->createMock(ActionInterface::class);

        $this->di = $this->createMock(Container::class);

        $this->subject = new FrontController(
            $this->dispatcher,
            $this->di,
            'route_error_handler',
            'method_error_handler'
        );
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchNotFound($isMagicInvocation)
    {
        $expectedResult = $this->createMock(ResponseInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withStatus');
        $response->expects($this->never())->method('withHeader');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::NOT_FOUND]);

        $this->handler->expects($this->once())->method('execute')->with($response)->willReturn($expectedResult);

        $this->di
            ->expects($this->once())
            ->method('newInstance')
            ->with('route_error_handler', [])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $response, $isMagicInvocation);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchMethodNotAllowed($isMagicInvocation)
    {
        $expectedResult = $this->createMock(ResponseInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withStatus');
        $response->expects($this->never())->method('withHeader');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, ['GET', 'POST']]);

        $this->handler->expects($this->once())->method('execute')->with($response)->willReturn($expectedResult);

        $this->di
            ->expects($this->once())
            ->method('newInstance')
            ->with('method_error_handler', ['allowedMethods' => ['GET', 'POST']])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $response, $isMagicInvocation);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchFoundInvalidAction($isMagicInvocation)
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withStatus');
        $response->expects($this->never())->method('withHeader');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::FOUND, 'fixture_action_type', ['param1' => 'value1', 'param2' => 'value2']]);

        $this->di
            ->expects($this->once())
            ->method('newInstance')
            ->with('fixture_action_type', ['param1' => 'value1', 'param2' => 'value2'])
            ->willReturn(new \stdClass());

        $actualResult = $this->invokeDispatch($this->subject, $response, $isMagicInvocation);

        $this->assertSame($response, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchFoundValidAction($isMagicInvocation)
    {
        $expectedResult = $this->createMock(ResponseInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withStatus');
        $response->expects($this->never())->method('withHeader');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::FOUND, 'fixture_action_type', ['param1' => 'value1', 'param2' => 'value2']]);

        $this->handler->expects($this->once())->method('execute')->with($response)->willReturn($expectedResult);

        $this->di
            ->expects($this->once())
            ->method('newInstance')
            ->with('fixture_action_type', ['param1' => 'value1', 'param2' => 'value2'])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $response, $isMagicInvocation);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function dispatchDataProvider()
    {
        return [
            'Explicit invocation' => [false],
            'Implicit invocation' => [true],
        ];
    }

    /**
     * @param FrontController $subject
     * @param ResponseInterface|MockObject $response
     * @param bool $isMagicInvocation
     * @return ResponseInterface
     */
    protected function invokeDispatch(FrontController $subject, ResponseInterface $response, $isMagicInvocation)
    {
        return $isMagicInvocation
            ? $subject($this->request, $response)
            : $subject->dispatch($this->request, $response);
    }
}
