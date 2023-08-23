<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainHistory;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainHistoryManagerTest extends TestCase
{
    protected Client $api;
    protected Domain $domain;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/domain/get.json')));
        $this->domain = $this->api->domains->get(366246);
    }

    public function testManagerCreation(): void
    {
        $this->assertInstanceOf(DomainHistoryManager::class, $this->domain->history);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/domainhistory/list.json')));
        $page = $this->domain->history->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(DomainHistory::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/history', $request->getUri()->getPath());
    }

    public function testFetchingSingleVersion(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);
        $this->assertInstanceOf(DomainHistory::class, $domainHistory);
        $this->assertEquals(3, $domainHistory->version);
        $this->assertSame($this->domain, $domainHistory->domain);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/history/3', $request->getUri()->getPath());
    }

    public function testApply(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);

        $this->mock->append(new Response(204, [], ''));
        $this->domain->history->apply($domainHistory);

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/history/3/apply', $request->getUri()->getPath());
    }

    public function testSnapshot(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);

        $this->mock->append(new Response(204, [], ''));
        $snapshot = $this->domain->history->snapshot($domainHistory);

        $this->assertInstanceOf(DomainSnapshot::class, $snapshot);
        $this->assertSame($this->domain, $snapshot->domain);
        $this->assertEquals(3, $snapshot->version);

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/history/3/snapshot', $request->getUri()->getPath());
    }
}
