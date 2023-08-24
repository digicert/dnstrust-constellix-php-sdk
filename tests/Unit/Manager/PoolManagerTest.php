<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Managers\PoolManager;
use Constellix\Client\Models\Pool;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class PoolManagerTest extends TestCase
{
    protected Client $api;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->pools;
        $this->assertInstanceOf(PoolManager::class, $manager);
    }
    public function testCreation(): void
    {
        $pool = $this->api->pools->create();
        $this->assertInstanceOf(Pool::class, $pool);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/list.json')));
        $page = $this->api->pools->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(Pool::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/pools', $request->getUri()->getPath());
    }

    public function testFetchingSinglePool(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertInstanceOf(Pool::class, $pool);
        $this->assertEquals(7665, $pool->id);
        $this->assertEquals(PoolType::A(), $pool->type);
        $this->assertTrue($pool->fullyLoaded);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/pools/a/7665', $request->getUri()->getPath());
    }

    public function testRefreshNoopOnNewPool(): void
    {
        $history = &$this->history();
        $pool = $this->api->pools->create();
        $pool->refresh();
        $this->assertCount(0, $history);
        $pool->id = 1;
        $pool->refresh();
        $this->assertCount(0, $history);
        $pool->id = null;
        $pool->type = PoolType::A();
        $pool->refresh();
        $this->assertCount(0, $history);
    }

    public function testRefresh(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));

        $this->assertCount(0, $history);
        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertCount(1, $history);
        $pool->refresh();
        $this->assertCount(2, $history);

        $request = $history[1]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/pools/a/7665', $request->getUri()->getPath());
    }

    public function testCacheIsUsedCorrectly(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));

        $this->assertCount(0, $history);
        $pool1 = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertCount(1, $history);
        $pool2 = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertCount(1, $history);
        $this->assertSame($pool1, $pool2);
    }

    public function testNoObjectFoundHandledCorrectly(): void
    {
        $this->mock->append(new Response(404, [], $this->getFixture('responses/error/notfound.json')));

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Unable to find object with Type A and ID 7665');

        $this->api->pools->get(PoolType::A(), 7665);
    }

    public function testApiReturnsNoData(): void
    {
        $this->mock->append(new Response(200, [], ''));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');

        $this->api->pools->get(PoolType::A(), 7665);
    }
}
