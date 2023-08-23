<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\DomainSnapshotManager;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainSnapshotManagerTest extends TestCase
{
    protected Client $api;
    protected Domain $domain;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $this->domain = $this->api->domains->get(366246);
    }

    public function testManagerCreation(): void
    {
        $this->assertInstanceOf(DomainSnapshotManager::class, $this->domain->snapshots);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/list.json')));
        $page = $this->domain->snapshots->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(DomainSnapshot::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/snapshots', $request->getUri()->getPath());
    }

    public function testFetchingSingleVersion(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);
        $this->assertInstanceOf(DomainSnapshot::class, $snapshot);
        $this->assertEquals(3, $snapshot->version);
        $this->assertSame($this->domain, $snapshot->domain);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/snapshots/3', $request->getUri()->getPath());
    }

    public function testApply(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->mock->append(new Response(204, [], ''));
        $this->domain->snapshots->apply($snapshot);

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/snapshots/3/apply', $request->getUri()->getPath());
    }
}
