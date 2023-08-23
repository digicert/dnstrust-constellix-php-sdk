<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\DomainRecordManager;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainRecord;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainRecordManagerTest extends TestCase
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
        $this->assertInstanceOf(DomainRecordManager::class, $this->domain->records);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/list.json')));
        $page = $this->domain->records->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(DomainRecord::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/records', $request->getUri()->getPath());
    }

    public function testFetchingSingleRecord(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);
        $this->assertInstanceOf(DomainRecord::class, $record);
        $this->assertEquals(732673, $record->id);
        $this->assertSame($this->domain, $record->domain);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/records/732673', $request->getUri()->getPath());
    }

    public function testCreation(): void
    {
        $record = $this->domain->records->create();
        $this->assertInstanceOf(DomainRecord::class, $record);
        $this->assertSame($this->domain, $record->domain);
    }
}
