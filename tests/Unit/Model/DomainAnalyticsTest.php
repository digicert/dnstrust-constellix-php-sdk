<?php

namespace Constellix\Client\Tests\Unit\Model;

use Carbon\Carbon;
use Constellix\Client\Client;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainAnalytics;
use Constellix\Client\Models\Helpers\Analytics\Interval;
use Constellix\Client\Models\Helpers\Analytics\Queries;
use Constellix\Client\Models\Helpers\Analytics\QueryType;
use Constellix\Client\Models\Helpers\Analytics\Stats;
use Constellix\Client\Models\Helpers\Analytics\Value;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainAnalyticsTest extends TestCase
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
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/analytics.json')));
        $analytics = $this->domain->getAnalytics(Carbon::now());
        $this->assertEquals('DomainAnalytics:2022011020220124', (string)$analytics);
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/analytics.json')));
        $analytics = $this->domain->getAnalytics(Carbon::now());

        $this->assertInstanceOf(DomainAnalytics::class, $analytics);
        $this->assertInstanceOf(Domain::class, $analytics->domain);
        $this->assertEquals(366246, $analytics->domain->id);

        $this->assertEquals(2022011020220124, $analytics->id);
        $this->assertInstanceOf(Carbon::class, $analytics->start);
        $this->assertEquals('Mon, 10 Jan 2022 00:00:00 +0000', $analytics->start->format('r'));
        $this->assertInstanceOf(Carbon::class, $analytics->end);
        $this->assertEquals('Mon, 24 Jan 2022 00:00:00 +0000', $analytics->end->format('r'));

        $this->assertInstanceOf(Stats::class, $analytics->stats);
        $this->assertEquals(1480, $analytics->stats->sum);
        $this->assertEquals(50, $analytics->stats->min);
        $this->assertEquals(201, $analytics->stats->max);
        $this->assertEquals(105.7143, $analytics->stats->mean);
        $this->assertEquals(14, $analytics->stats->count);

        $this->assertInstanceOf(Interval::class, $analytics->interval);
        $this->assertEquals(86398, $analytics->interval->min);
        $this->assertEquals(86401, $analytics->interval->max);
        $this->assertEquals(86399.5, $analytics->interval->mean);

        $this->assertInstanceOf(Queries::class, $analytics->queries);
        $this->assertInstanceOf(QueryType::class, $analytics->queries->standard);
        $this->assertNull($analytics->queries->geoFilter);
        $this->assertNull($analytics->queries->geoProximity);

        $this->assertCount(1, $analytics->queries->standard->values);

        $this->assertInstanceOf(Value::class, $analytics->queries->standard->values[0]);
        $this->assertInstanceOf(Carbon::class, $analytics->queries->standard->values[0]->date);
        $this->assertEquals('Mon, 10 Jan 2022 00:00:00 +0000', $analytics->queries->standard->values[0]->date->format('r'));
        $this->assertEquals(342, $analytics->queries->standard->values[0]->value);

        $this->assertInstanceOf(Stats::class, $analytics->queries->standard->stats);
        $this->assertEquals(1480, $analytics->queries->standard->stats->sum);
        $this->assertEquals(50, $analytics->queries->standard->stats->min);
        $this->assertEquals(201, $analytics->queries->standard->stats->max);
        $this->assertEquals(105.7143, $analytics->queries->standard->stats->mean);
        $this->assertEquals(14, $analytics->queries->standard->stats->count);
    }
}
