<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Helpers\NameserverGroup;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $list = $this->api->contactlists->create();
        $this->assertEquals('ContactList:#', (string)$list);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));
        $list = $this->api->contactlists->get(2668228);
        $this->assertEquals('ContactList:2668228', (string)$list);
    }

    public function testSaveOnContactList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlist/create.json')));

        $list = $this->api->contactlists->create();
        $list->name = 'My Contact List';

        $this->assertNull($list->id);
        $list->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/contactlists', $request->getUri()->getPath());

        $this->assertEquals(2668228, $list->id);
    }

    public function testSaveOnExistingContactList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));

        $list = $this->api->contactlists->get(82648967);
        $this->assertEquals('My Contact List', $list->name);

        $list->name = 'New Name';
        $this->assertTrue($list->hasChanged('name'));
        $this->assertEquals('New Name', $list->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));
        $list->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $list = $this->api->contactlists->create();
        $list->name = 'My Contact List';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlist/create.json')));
        $list->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/contactlist/create-simple.json'), $history[0]['request']->getBody());

        $list2 = $this->api->contactlists->create();
        $list2->name = 'My Contact List';
        $list2->addEmail('bob@example.com');
        $list2->addEmail('alice@example.com');

        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlist/create.json')));
        $list2->save();

        $this->assertCount(2, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/contactlist/create-complex.json'), $history[1]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));
        $list = $this->api->contactlists->get(2668228);

        $this->assertEquals(2668228, $list->id);
        $this->assertEquals('My Contact List', $list->name);
        $this->assertEquals(2, $list->emailCount);

        $this->assertCount(2, $list->emails);
        $this->assertEquals('bob@example.com', $list->emails[0]->address);
        $this->assertTrue($list->emails[0]->verified);
        $this->assertEquals('alice@example.com', $list->emails[1]->address);
        $this->assertFalse($list->emails[1]->verified);
    }

    public function testEmails(): void
    {
        $list = $this->api->contactlists->create();
        $this->assertCount(0, $list->emails);

        $list->addEmail('bob@example.com');
        $this->assertCount(1, $list->emails);


        // Adding the same email shouldn't increase the amount
        $list->addEmail('bob@example.com');
        $this->assertCount(1, $list->emails);
        $this->assertEquals('bob@example.com', $list->emails[0]->address);

        // Adding a new email should add it
        $list->addEmail('alice@example.com');
        $this->assertCount(2, $list->emails);
        $this->assertEquals('alice@example.com', $list->emails[1]->address);

        // Now removing an email should remove it
        $list->removeEmail('bob@example.com');
        $this->assertCount(1, $list->emails);

        // Removing it a second time will do nothing
        $list->removeEmail('bob@example.com');
        $this->assertCount(1, $list->emails);

        // Emails will re-index
        $this->assertSame('alice@example.com', $list->emails[0]->address);
    }
}
