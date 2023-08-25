<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainSnapshotTest extends TestCase
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
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->assertEquals('DomainSnapshot:3', (string)$snapshot);
    }

    public function testApply(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->assertCount(1, $history);

        $this->mock->append(new Response(204, [], ''));
        $snapshot->apply();
        $this->assertCount(2, $history);
        $this->assertEquals('POST', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246/snapshots/3/apply', $history[1]['request']->getUri()->getPath());
    }

    public function testDelete(): void
    {
        $history = &$this->history();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->assertCount(1, $history);

        $this->mock->append(new Response(204, [], ''));
        $snapshot->delete();

        $this->assertCount(2, $history);
        $this->assertEquals('DELETE', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/domains/366246/snapshots/3', $history[1]['request']->getUri()->getPath());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->assertEquals(3, $snapshot->id);
        $this->assertEquals(3, $snapshot->version);
        $this->assertSame($this->domain, $snapshot->domain);
        $this->assertEquals('2019-08-24T14:15:22+00:00', $snapshot->updatedAt->format('c'));
    }

    public function testJsoNSerializeDoesNotHaveId(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainsnapshot/get.json')));
        $snapshot = $this->domain->snapshots->get(3);

        $this->assertEquals(3, $snapshot->id);
        $json = $snapshot->jsonSerialize();
        $this->assertObjectNotHasProperty('id', $json);
    }
}
