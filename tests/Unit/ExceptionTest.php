<?php

namespace Constellix\Client\Tests\Unit;

use Constellix\Client\Enums\Pools\PoolValuePolicy;
use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ExceptionTest extends TestCase
{
    public function testHttpException(): void
    {
        $request = new Request(' GET', 'https://www.example.com');
        $response = new Response(500, [], 'Server Error');
        $exception = new HttpException();

        $this->assertNull($exception->getResponse());
        $this->assertNull($exception->getRequest());

        $exception->setResponse($response);
        $exception->setRequest($request);

        $this->assertEquals($response, $exception->getResponse());
        $this->assertEquals($request, $exception->getRequest());
    }
}
