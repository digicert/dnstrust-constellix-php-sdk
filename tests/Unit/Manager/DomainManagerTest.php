<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Models\Domain;
use Constellix\Client\Pagination\Paginator;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->domains;
        $this->assertInstanceOf(DomainManager::class, $manager);
    }

    public function testManagerIsReused(): void
    {
        $manager1 = $this->api->domains;
        $manager2 = $this->api->domains;
        $this->assertSame($manager1, $manager2);
    }

    public function testFetchingDomain(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertEquals(366246, $domain->id);
        $this->assertEquals('example.com', $domain->name);
        $this->assertTrue($domain->fullyLoaded);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246', $history[0]['request']->getUri()->getPath());
    }

    public function testFetchingNonExistantDomain(): void
    {
        $history = &$this->history();
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Unable to find object with ID 1234');
        $this->mock->append(new Response(404, [], $this->getFixture('responses/error/notfound.json')));
        $this->api->domains->get(1234);
        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/domains/1234', $history[0]['request']->getUri()->getPath());
    }

    public function testFetchingDomainWithNoData(): void
    {
        $history = &$this->history();
        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');
        $this->mock->append(new Response(200, [], ''));
        $this->api->domains->get(1234);
        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/domains/1234', $history[0]['request']->getUri()->getPath());
    }
    public function testDomainCreation(): void
    {
        $domain = $this->api->domains->create();
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertNull($domain->id);
    }

    public function testNewDomainSave(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/domain/create.json')));
        $domain = $this->api->domains->create();
        $domain->name = 'example.com';
        $domain->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains', $request->getUri()->getPath());

        $this->assertEquals(366246, $domain->id);

        $domain2 = $this->api->domains->create();
        $domain2->name = 'test.example.com';

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');
        $this->mock->append(new Response(200, [], ''));
        $domain2->save();
    }

    public function testExistingDomainSave(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $domain->note = 'Foobar';
        $domain->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/domains/366246', $request->getUri()->getPath());

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');
        $this->mock->append(new Response(200, [], ''));
        $domain->note = 'Foobar';
        $domain->save();
    }

    public function testCaching(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain1 = $this->api->domains->get(366246);
        $domain2 = $this->api->domains->get(366246);

        $this->assertCount(1, $history);
        $this->assertSame($domain1, $domain2);
    }

    public function testDeletingNewObject(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(204, [], ''));
        $domain = $this->api->domains->create();
        $domain->delete();

        $this->assertCount(0, $history);

        // Also test using manager directly, which shouldn't be used
        $this->api->domains->delete($domain);
        $this->assertCount(0, $history);
    }

    public function testDeletingExistingObject(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $this->mock->append(new Response(204, [], ''));
        $domain = $this->api->domains->get(366246);
        $domain->delete();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/v4/domains/366246', $request->getUri()->getPath());
    }

    public function testRefreshOnNewObjectIsNoop(): void
    {
        $history = &$this->history();
        $domain = $this->api->domains->create();
        $domain->refresh();
        $this->assertCount(0, $history);
    }

    public function testRefreshOnExistingObject(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertCount(1, $history);
        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246', $history[0]['request']->getUri()->getPath());

        $domain->refresh();
        $this->assertCount(2, $history);
        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246', $history[1]['request']->getUri()->getPath());
    }

    public function testCacheDirectly(): void
    {
        $this->assertNull($this->api->domains->getFromCache('Domain:366246'));

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);

        $domain2 = $this->api->domains->getFromCache('Domain:366246');
        $this->assertSame($domain, $domain2);

        $this->api->domains->removeFromCache('Domain:366246');
        $this->assertNull($this->api->domains->getFromCache('Domain:366246'));
    }

    public function testPaginationParameters(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/list.json')));
        $this->api->domains->paginate(5, 10, ['foo' => 'bar', 'thing' => 'other']);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];

        $this->assertEquals('foo=bar&thing=other&page=5&perPage=10', $request->getUri()->getQuery());
    }

    public function testCanOverridePaginationParameters(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/list.json')));
        $this->api->domains->paginate(5, 10, ['foo' => 'bar', 'thing' => 'other', 'page' => 100, 'perPage' => 50]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];

        $this->assertEquals('foo=bar&thing=other&page=100&perPage=50', $request->getUri()->getQuery());
    }

    public function testPaginationResultMatchesReturnedData(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/list.json')));
        $page = $this->api->domains->paginate(14, 1);

        $this->assertInstanceOf(Paginator::class, $page);
        $this->assertEquals(1, $page->count());
        $this->assertEquals(23, $page->total());
        $this->assertEquals(14, $page->currentPage());
        $this->assertEquals(1, $page->perPage());
        $this->assertEquals(23, $page->lastPage());
        $this->assertEquals(14, $page->firstItem());

        $this->assertInstanceOf(Domain::class, $page[0]);
    }

    public function testPaginationWithoutData(): void
    {
        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');
        $this->mock->append(new Response(200, [], ''));
        $this->api->domains->paginate();
    }
}
