<?php
namespace Upscale\HttpServerEngine\Tests\ErrorHandler;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\ErrorHandler\MethodNotAllowed;

class MethodNotAllowedTest extends TestCase
{
    /**
     * @var MethodNotAllowed
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new MethodNotAllowed(['GET', 'POST']);
    }

    public function testExecute()
    {
        /** @var ResponseInterface|MockObject $response */
        $expectedResult = $this->createMock(ResponseInterface::class);

        /** @var ResponseInterface|MockObject $partialResult */
        $partialResult = $this->createMock(ResponseInterface::class);
        $partialResult
            ->expects($this->once())
            ->method('withHeader')
            ->with('Allow', 'GET, POST')
            ->willReturn($expectedResult);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withStatus')->with(405)->willReturn($partialResult);

        $actualResult = $this->subject->execute($response);

        $this->assertSame($expectedResult, $actualResult);
    }
}
