<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\ContactList\TeamsWebhookManager;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\TeamsWebhook;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListTeamsManagerTest extends TestCase
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

    public function testManagerCreation(): void
    {
        $this->assertInstanceOf(TeamsWebhookManager::class, $this->contactList->teams);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistteams/list.json')));
        $page = $this->contactList->teams->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(TeamsWebhook::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/teams', $request->getUri()->getPath());
    }

    public function testFetchingSingleEmail(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistteams/get.json')));
        $teams = $this->contactList->teams->get(83267);
        $this->assertInstanceOf(TeamsWebhook::class, $teams);
        $this->assertEquals(83267, $teams->id);
        $this->assertSame($this->contactList, $teams->contactList);
        $this->assertTrue($teams->fullyLoaded);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/teams/83267', $request->getUri()->getPath());
    }

    public function testCreation(): void
    {
        $slack = $this->contactList->teams->create();
        $this->assertInstanceOf(TeamsWebhook::class, $slack);
        $this->assertSame($this->contactList, $slack->contactList);
    }
}
