<?php

namespace Constellix\Client\Tests\Unit;

use Carbon\Carbon;
use Carbon\Carbonite;
use Constellix\Client\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected MockHandler $mock;
    protected HttpClient $httpClient;

    protected HandlerStack $handlerStack;

    public function setUp(): void
    {
        $this->mock = new MockHandler();
        $this->handlerStack = new HandlerStack($this->mock);
        $this->httpClient = new HttpClient(['handler' => $this->handlerStack]);
    }

    public function beforeEach(): void
    {
        Carbonite::release();
        $this->handlerStack->remove('history');
        $this->mock->reset();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    protected function &history(): array
    {
        $history = [];
        $this->handlerStack->remove('history');
        $this->handlerStack->push(Middleware::history($history));
        return $history;
    }

    protected function getAuthenticatedClient(): Client
    {
        $apiClient = new Client($this->httpClient);
        $apiClient->setApiKey('1234');
        $apiClient->setSecretKey('5678');
        return $apiClient;
    }

    protected function getFixture(string $name): string
    {
        $filepath = __DIR__ . '/fixtures/' . $name;
        if (file_exists($filepath)) {
            return (string)file_get_contents($filepath);
        }
        return '';
    }
}
