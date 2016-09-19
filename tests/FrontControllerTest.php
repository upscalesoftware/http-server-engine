<?php
namespace Upscale\HttpServerEngine\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Upscale\HttpServerEngine\HandlerFactory;
use Upscale\HttpServerEngine\HandlerInterface;
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
     * @var HandlerFactory|MockObject
     */
    private $handlerFactory;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    /**
     * @var ResponseInterface|MockObject
     */
    private $inputResponse;

    /**
     * @var ResponseInterface|MockObject
     */
    private $expectedResponse;

    /**
     * @var HandlerInterface|MockObject
     */
    private $handler;

    protected function setUp()
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('getPath')->willReturn('/resource');

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->request->expects($this->once())->method('getMethod')->willReturn('TEST');
        $this->request->expects($this->once())->method('getUri')->willReturn($uri);

        $this->expectedResponse = $this->createMock(ResponseInterface::class);

        $this->inputResponse = $this->createMock(ResponseInterface::class);
        $this->inputResponse->expects($this->never())->method($this->anything());

        $this->handler = $this->createMock(HandlerInterface::class);
        $this->handler
            ->expects($this->once())
            ->method('execute')
            ->with($this->inputResponse)
            ->willReturn($this->expectedResponse);

        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->handlerFactory = $this->createMock(HandlerFactory::class);

        $this->subject = new FrontController(
            $this->dispatcher,
            $this->handlerFactory,
            'route_error_handler',
            'method_error_handler',
            'exception_handler'
        );
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchNotFound($isMagicInvocation)
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::NOT_FOUND]);

        $this->handlerFactory
            ->expects($this->once())
            ->method('create')
            ->with('route_error_handler', [])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $this->inputResponse, $isMagicInvocation);

        $this->assertSame($this->expectedResponse, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchMethodNotAllowed($isMagicInvocation)
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, ['GET', 'POST']]);

        $this->handlerFactory
            ->expects($this->once())
            ->method('create')
            ->with('method_error_handler', ['allowedMethods' => ['GET', 'POST']])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $this->inputResponse, $isMagicInvocation);

        $this->assertSame($this->expectedResponse, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchFoundInvalidAction($isMagicInvocation)
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::FOUND, 'fixture_action_type', ['param1' => 'value1', 'param2' => 'value2']]);

        $this->handlerFactory
            ->expects($this->at(0))
            ->method('create')
            ->with('fixture_action_type', ['param1' => 'value1', 'param2' => 'value2'])
            ->willThrowException(new \UnexpectedValueException());

        $this->handlerFactory
            ->expects($this->at(1))
            ->method('create')
            ->with('exception_handler', $this->logicalAnd(
                $this->arrayHasKey('exception'),
                $this->countOf(1),
                $this->containsOnlyInstancesOf(\UnexpectedValueException::class)
            ))
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $this->inputResponse, $isMagicInvocation);

        $this->assertSame($this->expectedResponse, $actualResult);
    }

    /**
     * @param bool $isMagicInvocation
     * @dataProvider dispatchDataProvider
     */
    public function testDispatchFoundValidAction($isMagicInvocation)
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('TEST', '/resource')
            ->willReturn([Dispatcher::FOUND, 'fixture_action_type', ['param1' => 'value1', 'param2' => 'value2']]);

        $this->handlerFactory
            ->expects($this->once())
            ->method('create')
            ->with('fixture_action_type', ['param1' => 'value1', 'param2' => 'value2'])
            ->willReturn($this->handler);

        $actualResult = $this->invokeDispatch($this->subject, $this->inputResponse, $isMagicInvocation);

        $this->assertSame($this->expectedResponse, $actualResult);
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
