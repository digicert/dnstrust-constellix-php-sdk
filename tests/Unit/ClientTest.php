<?php

namespace Constellix\Client\Tests\Unit;

use Carbon\Carbon;
use Carbon\Carbonite;
use Constellix\Client\Client;
use Constellix\Client\Exceptions\Client\Http\AuthenticationException;
use Constellix\Client\Exceptions\Client\Http\BadRequestException;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\JsonDecodeException;
use Constellix\Client\Exceptions\Client\ManagerNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Pagination\Factories\PaginatorFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\NullLogger;

class ClientTest extends TestCase
{
    public function testHttpClientIsRequired(): void
    {
        $apiClient = new Client();
        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No HTTP client has been specified');
        $apiClient->domains->get(1234);
    }

    public function testHttpClientIsSet(): void
    {
        $apiClient = new Client();
        $this->assertNull($apiClient->getHttpClient());
        $apiClient->setHttpClient($this->httpClient);
        $this->assertEquals($this->httpClient, $apiClient->getHttpClient());
    }

    public function testLoggerIsSet(): void
    {
        $apiClient = new Client();
        $logger = new NullLogger();
        $this->assertFalse($apiClient->getLogger() === $logger);
        $apiClient->setLogger($logger);
        $this->assertTrue($apiClient->getLogger() === $logger);
    }

    public function testEndpointIsSet(): void
    {
        $apiClient = new Client();
        $this->assertEquals('https://api.dns.constellix.com/v4', $apiClient->getEndpoint());
        $apiClient->setEndpoint('https://www.example.com');
        $this->assertEquals('https://www.example.com', $apiClient->getEndpoint());
    }

    public function testPaginatorIsSet(): void
    {
        $apiClient = new Client();
        $factory = new PaginatorFactory();
        $this->assertNotSame($apiClient->getPaginatorFactory(), $factory);
        $apiClient->setPaginatorFactory($factory);
        $this->assertSame($apiClient->getPaginatorFactory(), $factory);
    }

    public function testCredentialsAreSet(): void
    {
        $apiClient = new Client($this->httpClient);

        $this->assertNull($apiClient->getApiKey());
        $apiClient->setApiKey('1234');
        $this->assertEquals('1234', $apiClient->getApiKey());

        $this->assertNull($apiClient->getSecretKey());
        $apiClient->setSecretKey('abcd');
        $this->assertEquals('abcd', $apiClient->getSecretKey());
    }

    public function testLimitsParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [
            'X-RateLimit-Remaining' => '99',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Reset' => '20',
        ], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));

        $apiClient = $this->getAuthenticatedClient();

        $now = Carbon::now()->subMinutes(5);
        Carbonite::freeze($now);

        $apiClient->get('/domains/1');
        $this->assertEquals(100, $apiClient->getRequestLimit());
        $this->assertEquals(99, $apiClient->getRequestsRemaining());
        $limitReset = $apiClient->getLimitReset();
        $this->assertInstanceOf(Carbon::class, $limitReset);
        $this->assertEquals($now->getTimestamp() + 20, $limitReset->getTimestamp());
    }

    public function testMissingRateLimitsAreParsed(): void
    {
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));

        $apiClient = $this->getAuthenticatedClient();

        $this->assertNull($apiClient->getRequestLimit());
        $this->assertNull($apiClient->getRequestsRemaining());
        $this->assertNull($apiClient->getLimitReset());
    }

    public function testIncorrectManagerThrowsException(): void
    {
        $apiClient = new Client($this->httpClient);
        $this->expectException(ManagerNotFoundException::class);
        $manager = $apiClient->nonExistentManager;
    }

    public function testJsonDecodeErrorsHandledCorrectly(): void
    {
        $this->mock->append(new Response(200, [
            'Content-Type' => 'application/json',
        ], '{FOOBAR}}'));

        $apiClient = $this->getAuthenticatedClient();

        $this->expectException(JsonDecodeException::class);
        $this->expectExceptionMessage('Failed to decode request');

        $apiClient->domains->get(1234);
    }
    public function testError400HandledCorrectly(): void
    {
        $apiClient = $this->getAuthenticatedClient();

        $this->mock->append(new Response(400, [], 'Bad Request'));
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Bad Request');

        $apiClient->get('/domains/1');

        try {
            $apiClient->get('/domains/1');
        } catch (BadRequestException $ex) {
            $this->assertInstanceOf(RequestInterface::class, $ex->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $ex->getResponse());
            $this->assertEquals(400, $ex->getCode());
            throw $ex;
        }
    }
    public function testError401HandledCorrectly(): void
    {
        $apiClient = $this->getAuthenticatedClient();

        $this->mock->append(new Response(401, [], 'Unauthorized'));
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Unauthorized');

        $apiClient->get('/domains/1');

        try {
            $apiClient->domains->get(1);
        } catch (AuthenticationException $ex) {
            $this->assertInstanceOf(RequestInterface::class, $ex->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $ex->getResponse());
            $this->assertEquals(401, $ex->getCode());
            throw $ex;
        }
    }
    public function testError404HandledCorrectly(): void
    {
        $apiClient = $this->getAuthenticatedClient();

        $this->mock->append(new Response(404, [], 'Not Found'));
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Not Found');

        $apiClient->domains->paginate();

        try {
            $apiClient->get('/domains/1');
        } catch (NotFoundException $ex) {
            $this->assertInstanceOf(RequestInterface::class, $ex->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $ex->getResponse());
            $this->assertEquals(404, $ex->getCode());
            throw $ex;
        }
    }
    public function testError500HandledCorrectly(): void
    {
        $apiClient = $this->getAuthenticatedClient();

        $this->mock->append(new Response(500, [], 'Server Error'));
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Server Error');

        $apiClient->domains->get(1);

        try {
            $apiClient->domains->get(1);
        } catch (HttpException $ex) {
            $this->assertInstanceOf(RequestInterface::class, $ex->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $ex->getResponse());
            $this->assertEquals(500, $ex->getCode());
            throw $ex;
        }
    }

    public function testGetRequests(): void
    {
        $history = &$this->history();
        $client = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $params = [
            'foo' => 'bar',
            'name' => 'example',
        ];
        $client->get('/domains/1', $params);
        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertContains('application/json', $history[0]['request']->getHeader('Accept'));
        $this->assertEquals('https://api.dns.constellix.com/v4/domains/1?foo=bar&name=example', (string)$history[0]['request']->getUri());
    }

    public function testPostRequests(): void
    {
        $history = &$this->history();
        $client = $this->getAuthenticatedClient();
        $this->mock->append(new Response(201, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $params = [
            'name' => 'example.com',
        ];
        $client->post('/domains', $params);
        $this->assertEquals('POST', $history[0]['request']->getMethod());
        $this->assertJson('{"name":"example.com"}', $history[0]['request']->getBody());
        $this->assertContains('application/json', $history[0]['request']->getHeader('Accept'));
        $this->assertContains('application/json', $history[0]['request']->getHeader('Content-Type'));
        $this->assertEquals('https://api.dns.constellix.com/v4/domains', (string)$history[0]['request']->getUri());

        $this->mock->append(new Response(201, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $client->post('/domains');
        // An empty body will just be the php://temp stream
        $this->assertEquals('php://temp', (string)$history[1]['request']->getBody());
        $this->assertEmpty($history[1]['request']->getHeader('Content-Type'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to encode API payload');
        $handle = tmpfile();
        $client->post('/domains', $handle);
        if ($handle !== false) {
            fclose($handle);
        }
    }

    public function testPutRequests(): void
    {
        $history = &$this->history();
        $client = $this->getAuthenticatedClient();
        $this->mock->append(new Response(201, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $params = [
            'name' => 'example.com',
        ];
        $client->put('/domains/1', $params);
        $this->assertEquals('PUT', $history[0]['request']->getMethod());
        $this->assertJson('{"name":"example.com"}', $history[0]['request']->getBody());
        $this->assertContains('application/json', $history[0]['request']->getHeader('Accept'));
        $this->assertContains('application/json', $history[0]['request']->getHeader('Content-Type'));
        $this->assertEquals('https://api.dns.constellix.com/v4/domains/1', (string)$history[0]['request']->getUri());

        $this->mock->append(new Response(201, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $client->put('/domains/1');
        // An empty body will just be the php://temp stream
        $this->assertEquals('php://temp', (string)$history[1]['request']->getBody());
        $this->assertEmpty($history[1]['request']->getHeader('Content-Type'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to encode API payload');
        $handle = tmpfile();
        $client->put('/domains/1', $handle);
        if ($handle !== false) {
            fclose($handle);
        }
    }

    public function testDeleteRequests(): void
    {
        $history = &$this->history();
        $client = $this->getAuthenticatedClient();
        $this->mock->append(new Response(201, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $params = [
            'name' => 'example.com',
        ];
        $client->delete('/domains/1', $params);
        $this->assertEquals('DELETE', $history[0]['request']->getMethod());
        $this->assertJson('{"name":"example.com"}', $history[0]['request']->getBody());
        $this->assertContains('application/json', $history[0]['request']->getHeader('Accept'));
        $this->assertContains('application/json', $history[0]['request']->getHeader('Content-Type'));
        $this->assertEquals('https://api.dns.constellix.com/v4/domains/1', (string)$history[0]['request']->getUri());

        $this->mock->append(new Response(204, [], ''));
        $client->delete('/domains/1');
        // An empty body for DELETE is an empty string
        $this->assertEquals('', (string)$history[1]['request']->getBody());
        $this->assertEmpty($history[1]['request']->getHeader('Content-Type'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to encode API payload');
        $handle = tmpfile();
        $client->delete('/domains/1', $handle);
        if ($handle !== false) {
            fclose($handle);
        }
    }

    public function testAuthentication(): void
    {
        $apiClient = $this->getAuthenticatedClient();

        Carbonite::freeze('2023-01-01 00:00:00');

        $history = &$this->history();

        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/fixtures/domain/get.json')));
        $apiClient->get('/domains/1');

        $this->assertEquals('Bearer 1234:LZ6jDnnsZ+mehbpdBUSt5c+8FNk=:1672531200000', $history[0]['request']->getHeader('Authorization')[0]);
    }
}
