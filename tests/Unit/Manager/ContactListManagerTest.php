<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->contactlists;
        $this->assertInstanceOf(ContactListManager::class, $manager);
    }


    public function testFetchingContactList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/contactlist/get.json')));
        $contactList = $this->api->contactlists->get(2668228);
        $this->assertInstanceOf(ContactList::class, $contactList);
        $this->assertEquals(2668228, $contactList->id);
        $this->assertEquals('My Contact List', $contactList->name);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/contactlists/2668228', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingContactList(): void
    {
        $contactList = $this->api->contactlists->create();
        $this->assertInstanceOf(ContactList::class, $contactList);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/contactlist/list.json')));
        $page = $this->api->contactlists->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(ContactList::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/contactlists', $history[0]['request']->getUri()->getPath());
    }
}
