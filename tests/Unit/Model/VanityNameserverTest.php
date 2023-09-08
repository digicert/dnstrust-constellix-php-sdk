<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Helpers\NameserverGroup;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class VanityNameserverTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $nameserver = $this->api->vanitynameservers->create();
        $this->assertEquals('VanityNameserver:#', (string)$nameserver);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));
        $nameserver = $this->api->vanitynameservers->get(82648967);
        $this->assertEquals('VanityNameserver:82648967', (string)$nameserver);
    }

    public function testSaveOnNewNameserver(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/vanitynameserver/create.json')));

        $vanity = $this->api->vanitynameservers->create();
        $vanity->name = 'My Vanity Nameserver';

        $this->assertNull($vanity->id);
        $vanity->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/vanitynameservers', $request->getUri()->getPath());

        $this->assertEquals(82648967, $vanity->id);
    }

    public function testSaveOnExistingVanityNameserver(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));

        $vanity = $this->api->vanitynameservers->get(82648967);
        $this->assertEquals('My Vanity nameserver', $vanity->name);

        $vanity->name = 'New Name';
        $this->assertTrue($vanity->hasChanged('name'));
        $this->assertEquals('New Name', $vanity->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));
        $vanity->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/vanitynameservers/82648967', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $vanity = $this->api->vanitynameservers->create();
        $vanity->name = 'My Vanity Nameserver';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/vanitynameserver/create.json')));
        $vanity->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/vanitynameserver/create-simple.json'), $history[0]['request']->getBody());

        $vanity2 = $this->api->vanitynameservers->create();
        $vanity2->name = 'My Vanity Nameserver';
        $vanity2->default = false;
        $vanity2->nameserverGroup->id = 2;
        $vanity2->addNameserver('ns1.example.com');
        $vanity2->addNameserver('ns2.example.com');

        $this->mock->append(new Response(201, [], $this->getFixture('responses/vanitynameserver/create.json')));
        $vanity2->save();

        $this->assertCount(2, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/vanitynameserver/create-complex.json'), $history[1]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));
        $vanity = $this->api->vanitynameservers->get(82648967);

        $this->assertEquals(82648967, $vanity->id);
        $this->assertEquals('My Vanity nameserver', $vanity->name);
        $this->assertFalse($vanity->default);
        $this->assertFalse($vanity->public);

        $this->assertInstanceOf(NameserverGroup::class, $vanity->nameserverGroup);
        $this->assertEquals(674, $vanity->nameserverGroup->id);
        $this->assertEquals('Nameserver Group 1', $vanity->nameserverGroup->name);

        $this->assertCount(2, $vanity->nameservers);
        $this->assertEquals('ns1.example.com', $vanity->nameservers[0]);
        $this->assertEquals('ns2.example.com', $vanity->nameservers[1]);
    }

    public function testNameservers(): void
    {
        $vanity = $this->api->vanitynameservers->create();
        $this->assertCount(0, $vanity->nameservers);

        $vanity->addNameServer('ns1.example.com');
        $this->assertCount(1, $vanity->nameservers);


        // Adding the same nameserver shouldn't increase the amount
        $vanity->addNameServer('ns1.example.com');
        $this->assertCount(1, $vanity->nameservers);
        $this->assertEquals('ns1.example.com', $vanity->nameservers[0]);

        // Adding a new nameserver should add it
        $vanity->addNameServer('ns2.example.com');
        $this->assertCount(2, $vanity->nameservers);
        $this->assertEquals('ns2.example.com', $vanity->nameservers[1]);

        // Now removing a nameserver should remove it
        $vanity->removeNameServer('ns1.example.com');
        $this->assertCount(1, $vanity->nameservers);

        // Removing it a second time will do nothing
        $vanity->removeNameServer('ns1.example.com');
        $this->assertCount(1, $vanity->nameservers);

        // Nameservers will re-index
        $this->assertEquals('ns2.example.com', $vanity->nameservers[0]);
    }
}
