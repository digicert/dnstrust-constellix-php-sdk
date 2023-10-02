<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\SlackWebhook;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListSlackTest extends TestCase
{
    protected Client $api;
    protected ContactList $contactList;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));
        $this->contactList = $this->api->contactlists->get(2668228);
    }
    public function testSaveOnNewWebhook(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistslack/create.json')));

        $webhook = $this->contactList->slack->create();
        $webhook->channel = 'dnsalerts';
        $webhook->webhook = 'https://hooks.slack.com/services/T1234/B4321/ABCD1234';

        $this->assertNull($webhook->id);

        $webhook->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/slack', $request->getUri()->getPath());

        $this->assertEquals(75225, $webhook->id);
    }

    public function testSaveOnExistingWebhook(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistslack/get.json')));

        $webhook = $this->contactList->slack->get(75225);
        $this->assertEquals('dnsalerts', $webhook->channel);

        $webhook->channel = 'tech';
        $this->assertTrue($webhook->hasChanged('channel'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to update existing Contact List Slack Webhook objects');
        $webhook->save();
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $webhook = $this->contactList->slack->create();
        $webhook->channel = 'dnsalerts';
        $webhook->webhook = 'https://hooks.slack.com/services/T1234/B4321/ABCD1234';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistslack/create.json')));
        $webhook->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/contactlistslack/create.json'), $history[0]['request']->getBody());
    }
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistslack/get.json')));
        $webhook = $this->contactList->slack->get(75225);

        $this->assertInstanceOf(SlackWebhook::class, $webhook);
        $this->assertSame($this->contactList, $webhook->contactList);
        $this->assertEquals(75225, $webhook->id);
        $this->assertEquals('https://hooks.slack.com/services/T1234/B4321/ABCD1234', $webhook->webhook);
        $this->assertEquals('dnsalerts', $webhook->channel);
    }
}
