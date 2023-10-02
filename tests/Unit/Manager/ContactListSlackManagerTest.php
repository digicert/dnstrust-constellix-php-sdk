<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\ContactList\SlackWebhookManager;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\SlackWebhook;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListSlackManagerTest extends TestCase
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
        $this->assertInstanceOf(SlackWebhookManager::class, $this->contactList->slack);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistslack/list.json')));
        $page = $this->contactList->slack->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(SlackWebhook::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/slack', $request->getUri()->getPath());
    }

    public function testFetchingSingleEmail(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistslack/get.json')));
        $slack = $this->contactList->slack->get(75225);
        $this->assertInstanceOf(SlackWebhook::class, $slack);
        $this->assertEquals(75225, $slack->id);
        $this->assertSame($this->contactList, $slack->contactList);
        $this->assertTrue($slack->fullyLoaded);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/slack/75225', $request->getUri()->getPath());
    }

    public function testCreation(): void
    {
        $slack = $this->contactList->slack->create();
        $this->assertInstanceOf(SlackWebhook::class, $slack);
        $this->assertSame($this->contactList, $slack->contactList);
    }
}
