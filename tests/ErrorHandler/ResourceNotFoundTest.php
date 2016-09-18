<?php
namespace Upscale\HttpServerEngine\Tests\ErrorHandler;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\ErrorHandler\ResourceNotFound;

class ResourceNotFoundTest extends TestCase
{
    /**
     * @var ResourceNotFound
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new ResourceNotFound();
    }

    public function testExecute()
    {
        /** @var ResponseInterface|MockObject $response */
        $expectedResult = $this->createMock(ResponseInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withStatus')->with(404)->willReturn($expectedResult);

        $actualResult = $this->subject->execute($response);

        $this->assertSame($expectedResult, $actualResult);
    }
}
