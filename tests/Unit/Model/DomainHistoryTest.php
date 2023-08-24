<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainHistoryTest extends TestCase
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

    public function testToString(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);

        $this->assertEquals('DomainHistory:3', (string)$domainHistory);
    }

    public function testApply(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);

        $this->assertCount(1, $history);

        $this->mock->append(new Response(204, [], ''));
        $domainHistory->apply();
        $this->assertCount(2, $history);
        $this->assertEquals('POST', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246/history/3/apply', $history[1]['request']->getUri()->getPath());
    }

    public function testSnapshot(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainhistory/get.json')));
        $domainHistory = $this->domain->history->get(3);

        $this->assertCount(1, $history);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainsnapshot/create.json')));
        $snapshot = $domainHistory->snapshot();
        $this->assertInstanceOf(DomainSnapshot::class, $snapshot);
        $this->assertEquals(4, $snapshot->version);

        $this->assertCount(2, $history);
        $this->assertEquals('POST', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246/history/3/snapshot', $history[1]['request']->getUri()->getPath());
    }
}
