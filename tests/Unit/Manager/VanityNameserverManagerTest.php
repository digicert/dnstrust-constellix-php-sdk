<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\VanityNameserverManager;
use Constellix\Client\Models\VanityNameserver;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class VanityNameserverManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->vanitynameservers;
        $this->assertInstanceOf(VanityNameserverManager::class, $manager);
    }


    public function testFetchingVanityNameservers(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));
        $nameserver = $this->api->vanitynameservers->get(82648967);
        $this->assertInstanceOf(VanityNameserver::class, $nameserver);
        $this->assertEquals(82648967, $nameserver->id);
        $this->assertEquals('My Vanity nameserver', $nameserver->name);
        $this->assertTrue($nameserver->fullyLoaded);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/vanitynameservers/82648967', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingVanityNameserver(): void
    {
        $tag = $this->api->vanitynameservers->create();
        $this->assertInstanceOf(VanityNameserver::class, $tag);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/list.json')));
        $page = $this->api->vanitynameservers->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(VanityNameserver::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/vanitynameservers', $history[0]['request']->getUri()->getPath());
    }
}
