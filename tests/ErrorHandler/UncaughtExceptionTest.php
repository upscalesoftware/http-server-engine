<?php
namespace Upscale\HttpServerEngine\Tests\ErrorHandler;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Upscale\HttpServerEngine\ErrorHandler\UncaughtException;

class UncaughtExceptionTest extends TestCase
{
    /**
     * @var UncaughtException
     */
    private $subject;

    /**
     * @var ResponseInterface|MockObject
     */
    private $response;

    /**
     * @var StreamInterface|MockObject
     */
    private $responseBody;

    protected function setUp()
    {
        $this->responseBody = $this->createMock(StreamInterface::class);

        $this->response = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())->method('getBody')->willReturn($this->responseBody);

        $this->subject = new UncaughtException(new \Exception('Unexpected error'));
    }

    public function testExecute()
    {
        /** @var ResponseInterface|MockObject $response */
        $expectedResult = $this->createMock(ResponseInterface::class);

        $this->responseBody->expects($this->once())->method('write')->with('Unexpected error');

        $this->response->expects($this->once())->method('withStatus')->with(500)->willReturn($expectedResult);

        $actualResult = $this->subject->execute($this->response);

        $this->assertSame($expectedResult, $actualResult);
    }
}
