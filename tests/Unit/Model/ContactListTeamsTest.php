<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\TeamsWebhook;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListTeamsTest extends TestCase
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
        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistteams/create.json')));

        $webhook = $this->contactList->teams->create();
        $webhook->channel = 'dnsalerts';
        $webhook->webhook = 'https://outlook.office.com/webhook/123456789';

        $this->assertNull($webhook->id);

        $webhook->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/teams', $request->getUri()->getPath());

        $this->assertEquals(83267, $webhook->id);
    }

    public function testSaveOnExistingWebhook(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistteams/get.json')));

        $webhook = $this->contactList->teams->get(83267);
        $this->assertEquals('dnsalerts', $webhook->channel);

        $webhook->channel = 'tech';
        $this->assertTrue($webhook->hasChanged('channel'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to update existing Contact List Teams Webhook objects');
        $webhook->save();
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $webhook = $this->contactList->teams->create();
        $webhook->channel = 'dnsalerts';
        $webhook->webhook = 'https://outlook.office.com/webhook/123456789';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistteams/create.json')));
        $webhook->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/contactlistteams/create.json'), $history[0]['request']->getBody());
    }
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistteams/get.json')));
        $webhook = $this->contactList->teams->get(83267);

        $this->assertInstanceOf(TeamsWebhook::class, $webhook);
        $this->assertSame($this->contactList, $webhook->contactList);
        $this->assertEquals(83267, $webhook->id);
        $this->assertEquals('https://outlook.office.com/webhook/123456789', $webhook->webhook);
        $this->assertEquals('dnsalerts', $webhook->channel);
    }
}
