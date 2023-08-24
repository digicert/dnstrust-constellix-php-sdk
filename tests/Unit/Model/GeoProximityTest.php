<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class GeoProximityTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $geoproximity = $this->api->geoproximity->create();
        $this->assertEquals('GeoProximity:#', (string)$geoproximity);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/geoproximity/get.json')));
        $tag = $this->api->geoproximity->get(4367769);
        $this->assertEquals('GeoProximity:4367769', (string)$tag);
    }

    public function testSaveOnNewGeoProximity(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/geoproximity/create.json')));

        $geoproximity = $this->api->geoproximity->create();
        $geoproximity->name = 'My Tag';

        $this->assertNull($geoproximity->id);
        $geoproximity->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/geoproximities', $request->getUri()->getPath());

        $this->assertEquals(4367769, $geoproximity->id);
    }

    public function testSaveOnExistingTag(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/geoproximity/get.json')));

        $geoproximity = $this->api->geoproximity->get(4367769);
        $this->assertEquals('My Geo Proximity Location', $geoproximity->name);

        $geoproximity->name = 'New Name';
        $this->assertTrue($geoproximity->hasChanged('name'));
        $this->assertEquals('New Name', $geoproximity->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/geoproximity/get.json')));
        $geoproximity->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/geoproximities/4367769', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $geoproximity = $this->api->geoproximity->create();
        $geoproximity->name = 'My Geo Proximity Location';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/geoproximity/create.json')));
        $geoproximity->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/geoproximity/create-simple.json'), $history[0]['request']->getBody());

        $geoproximity2 = $this->api->geoproximity->create();
        $geoproximity2->name = 'My Geo Proximity Location';
        $geoproximity2->country = 'GB';
        $geoproximity2->region = 'London';
        $geoproximity2->city = 1;
        $geoproximity2->longitude = -0.1275;
        $geoproximity2->latitude = 51.5033;

        $this->mock->append(new Response(201, [], $this->getFixture('responses/geoproximity/create.json')));
        $geoproximity2->save();

        $this->assertCount(2, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/geoproximity/create-complex.json'), $history[1]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/geoproximity/get.json')));
        $geoproximity = $this->api->geoproximity->get(4367769);

        $this->assertEquals(4367769, $geoproximity->id);
        $this->assertEquals('My Geo Proximity Location', $geoproximity->name);
        $this->assertEquals('GB', $geoproximity->country);
        $this->assertEquals('Greater London', $geoproximity->region);
        $this->assertEquals(58898, $geoproximity->city);
        $this->assertEquals(-0.1275, $geoproximity->longitude);
        $this->assertEquals(51.5033, $geoproximity->latitude);
    }
}
