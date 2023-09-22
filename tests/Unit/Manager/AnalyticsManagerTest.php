<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Carbon\Carbon;
use Carbon\Carbonite;
use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Managers\AnalyticsManager;
use Constellix\Client\Models\Analytics;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class AnalyticsManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->analytics;
        $this->assertInstanceOf(AnalyticsManager::class, $manager);
    }


    public function testFetchingAnalytics(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/analytics/get.json')));

        $start = new Carbon('2023-09-01 00:00:00');
        $end = new Carbon('2023-09-08 00:00:00');
        $analytics = $this->api->analytics->get($start, $end);
        $this->assertInstanceOf(Analytics::class, $analytics);

        $this->assertEquals('Mon, 10 Jan 2022 00:00:00 +0000', $analytics->start->format('r'));
        $this->assertTrue($analytics->fullyLoaded);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/analytics', $history[0]['request']->getUri()->getPath());
        $this->assertEquals('start=20230901&end=20230908', $history[0]['request']->getUri()->getQuery());
    }

    public function testDefaultValueForEndDate(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/analytics/get.json')));

        Carbonite::freeze('2023-09-08 00:00:00');

        $start = new Carbon('2023-09-01 00:00:00');
        $this->api->analytics->get($start);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/analytics', $history[0]['request']->getUri()->getPath());
        $this->assertEquals("start=20230901&end=20230908", $history[0]['request']->getUri()->getQuery());
        Carbonite::release();
    }

    public function testNoApiResponse(): void
    {
        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('No data returned from API');
        $this->mock->append(new Response(200, [], ''));

        $start = new Carbon('2023-09-01 00:00:00');
        $this->api->analytics->get($start);
    }
}
