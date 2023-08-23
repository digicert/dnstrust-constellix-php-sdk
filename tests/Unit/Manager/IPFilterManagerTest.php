<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Models\IPFilter;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class IPFilterManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->ipfilters;
        $this->assertInstanceOf(IPFilterManager::class, $manager);
    }


    public function testFetchingIPFilters(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));
        $ipfilter = $this->api->ipfilters->get(47345837);
        $this->assertInstanceOf(IPFilter::class, $ipfilter);
        $this->assertEquals(47345837, $ipfilter->id);
        $this->assertEquals('My IP filter', $ipfilter->name);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/ipfilters/47345837', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingIPFilter(): void
    {
        $ipfilter = $this->api->ipfilters->create();
        $this->assertInstanceOf(IPFilter::class, $ipfilter);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/list.json')));
        $page = $this->api->ipfilters->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(IPFilter::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/ipfilters', $history[0]['request']->getUri()->getPath());
    }
}
