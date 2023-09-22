<?php

namespace Constellix\Client\Tests\Unit\Model;

use Carbon\Carbon;
use Constellix\Client\Client;
use Constellix\Client\Models\Analytics;
use Constellix\Client\Models\Helpers\Analytics\Interval;
use Constellix\Client\Models\Helpers\Analytics\Stats;
use Constellix\Client\Models\Helpers\Analytics\Value;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class AnalyticsTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/analytics/get.json')));
        $analytics = $this->api->analytics->get(Carbon::now());
        $this->assertEquals('Analytics:2022011020220124', (string)$analytics);
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/analytics/get.json')));
        $analytics = $this->api->analytics->get(Carbon::now());

        $this->assertInstanceOf(Analytics::class, $analytics);

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

        $this->assertCount(1, $analytics->values);
        $this->assertInstanceOf(Value::class, $analytics->values[0]);
        $this->assertInstanceOf(Carbon::class, $analytics->values[0]->date);
        $this->assertEquals('Mon, 10 Jan 2022 00:00:00 +0000', $analytics->values[0]->date->format('r'));
        $this->assertEquals(342, $analytics->values[0]->value);
    }
}
