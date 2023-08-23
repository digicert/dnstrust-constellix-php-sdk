<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\GeoProximityManager;
use Constellix\Client\Models\GeoProximity;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class GeoProximityManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->geoproximity;
        $this->assertInstanceOf(GeoProximityManager::class, $manager);
    }


    public function testFetchingGeoProximities(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/geoproximity/get.json')));
        $geoproximity = $this->api->geoproximity->get(4367769);
        $this->assertInstanceOf(GeoProximity::class, $geoproximity);
        $this->assertEquals(4367769, $geoproximity->id);
        $this->assertEquals('My Geo Proximity Location', $geoproximity->name);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/geoproximities/4367769', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingGeoProximity(): void
    {
        $geoproximity = $this->api->geoproximity->create();
        $this->assertInstanceOf(GeoProximity::class, $geoproximity);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/geoproximity/list.json')));
        $page = $this->api->geoproximity->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(GeoProximity::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/geoproximities', $history[0]['request']->getUri()->getPath());
    }
}
