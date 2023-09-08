<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Enums\Continent;
use Constellix\Client\Models\Helpers\IPFilterRegion;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class IPFilterTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $tag = $this->api->ipfilters->create();
        $this->assertEquals('IPFilter:#', (string)$tag);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));
        $tag = $this->api->ipfilters->get(824);
        $this->assertEquals('IPFilter:47345837', (string)$tag);
    }

    public function testSaveOnNewIPFilter(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/ipfilter/create.json')));

        $filter = $this->api->ipfilters->create();
        $filter->name = 'My IP filter';

        $this->assertNull($filter->id);
        $filter->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/ipfilters', $request->getUri()->getPath());

        $this->assertEquals(47345837, $filter->id);
    }

    public function testSaveOnExistingTag(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));

        $filter = $this->api->tags->get(47345837);
        $this->assertEquals('My IP filter', $filter->name);

        $filter->name = 'New Name';
        $this->assertTrue($filter->hasChanged('name'));
        $this->assertEquals('New Name', $filter->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));
        $filter->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/tags/47345837', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $filter = $this->api->ipfilters->create();
        $filter->name = 'My IP filter';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/ipfilter/create.json')));
        $filter->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/ipfilter/create-simple.json'), $history[0]['request']->getBody());

        $filter2 = $this->api->ipfilters->create();
        $filter2->name = 'My IP filter';

        $filter2->addContinent(Continent::EU());
        $filter2->addContinent(Continent::NA());

        $filter2->addCountry('GB');
        $filter2->addCountry('FR');
        $filter2->addCountry('DE');

        $filter2->addASN(64496);
        $filter2->addASN(64499);

        $filter2->addIPv4('198.51.100.0/24');
        $filter2->addIPv4('203.0.113.42');

        $filter2->addIPv6('2001:db8:200::/64');
        $filter2->addIPv6('2001:db8:200:42::');

        $region = new IPFilterRegion();
        $region->continent = Continent::EU();
        $region->country = 'GB';
        $region->region = 'EN';
        $region->asn = 64499;

        $filter2->addRegion($region);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/ipfilter/create.json')));
        $filter2->save();

        $this->assertCount(2, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/ipfilter/create-complex.json'), $history[1]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));
        $filter = $this->api->ipfilters->get(47345837);

        $this->assertEquals(47345837, $filter->id);
        $this->assertEquals('My IP filter', $filter->name);
        $this->assertEquals(100, $filter->rulesLimit);

        $this->assertCount(2, $filter->continents);
        $this->assertEquals(Continent::EU(), $filter->continents[0]);
        $this->assertEquals(Continent::NA(), $filter->continents[1]);

        $this->assertCount(2, $filter->countries);
        $this->assertEquals('GB', $filter->countries[0]);
        $this->assertEquals('DE', $filter->countries[1]);

        $this->assertCount(1, $filter->regions);
        $this->assertInstanceOf(IPFilterRegion::class, $filter->regions[0]);
        $this->assertInstanceOf(Continent::class, $filter->regions[0]->continent);
        $this->assertEquals('US', $filter->regions[0]->country);
        $this->assertEquals('FL', $filter->regions[0]->region);
        $this->assertEquals(64496, $filter->regions[0]->asn);

        $this->assertCount(2, $filter->asn);
        $this->assertEquals(64496, $filter->asn[0]);
        $this->assertEquals(64499, $filter->asn[1]);

        $this->assertCount(2, $filter->ipv4);
        $this->assertEquals('198.51.100.0/24', $filter->ipv4[0]);
        $this->assertEquals('203.0.113.42', $filter->ipv4[1]);

        $this->assertCount(2, $filter->ipv6);
        $this->assertEquals('2001:db8:200::/64', $filter->ipv6[0]);
        $this->assertEquals('2001:db8:200:42::', $filter->ipv6[1]);
    }

    // Referenced Collections (there are a lot!)
    public function testCollections(): void
    {
        $region1 = new IPFilterRegion();
        $region1->continent = Continent::NA();

        $region2 = new IPFilterRegion();
        $region2->continent = Continent::EU();

        $collections = [
            'Continent' => (object) [
                'value1' => Continent::NA(),
                'value2' => Continent::EU(),
                'property' => 'continents',
            ],
            'Country' => (object) [
                'value1' => 'US',
                'value2' => 'GB',
                'property' => 'countries',
            ],
            'ASN' => (object) [
                'value1' => 64496,
                'value2' => 64499,
                'property' => 'asn',
            ],
            'IPv4' => (object) [
                'value1' => '198.51.100.0/24',
                'value2' => '203.0.113.42',
                'property' => 'ipv4',
            ],
            'IPv6' => (object) [
                'value1' => '2001:db8:200::/64',
                'value2' => '2001:db8:200:42::',
                'property' => 'ipv6',
            ],
            'Region' => (object) [
                'value1' => $region1,
                'value2' =>  $region2,
                'property' => 'regions',
            ],
        ];

        $filter = $this->api->ipfilters->create();

        foreach ($collections as $name => $config) {
            $addMethod = "add{$name}";
            $removeMethod = "remove{$name}";

            $value1 = $config->value1;
            $value2 = $config->value2;
            $property = $config->property;

            $this->assertCount(0, $filter->{$property});
            $filter->$addMethod($value1);
            $this->assertCount(1, $filter->{$property});
            $this->assertSame($value1, $filter->{$property}[0]);

            $filter->$addMethod($value1);
            $this->assertCount(1, $filter->{$property});

            $filter->$addMethod($value2);
            $this->assertCount(2, $filter->{$property});
            $this->assertSame($value2, $filter->{$property}[1]);

            $filter->$removeMethod($value1);
            $this->assertCount(1, $filter->{$property});

            $filter->$removeMethod($value1);
            $this->assertCount(1, $filter->{$property});

            $this->assertSame($value2, $filter->{$property}[0]);
        }
    }
}
