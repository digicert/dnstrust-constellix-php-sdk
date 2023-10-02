<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\ContactList\EmailManager;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\Email;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListEmailManagerTest extends TestCase
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
        $this->assertInstanceOf(EmailManager::class, $this->contactList->emails);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistemail/list.json')));
        $page = $this->contactList->emails->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(Email::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/emails', $request->getUri()->getPath());
    }

    public function testFetchingSingleEmail(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistemail/get.json')));
        $email = $this->contactList->emails->get(36245);
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals(36245, $email->id);
        $this->assertSame($this->contactList, $email->contactList);
        $this->assertTrue($email->fullyLoaded);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/emails/36245', $request->getUri()->getPath());
    }

    public function testCreation(): void
    {
        $email = $this->contactList->emails->create();
        $this->assertInstanceOf(Email::class, $email);
        $this->assertSame($this->contactList, $email->contactList);
    }
}
