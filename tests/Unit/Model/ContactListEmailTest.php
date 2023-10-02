<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\ContactLists\Email;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactListEmailTest extends TestCase
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
    public function testSaveOnNewEmail(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistemail/create.json')));

        $email = $this->contactList->emails->create();
        $email->address = 'bob@example.com';

        $this->assertNull($email->id);

        $email->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/contactlists/2668228/emails', $request->getUri()->getPath());

        $this->assertEquals(366246, $email->id);
    }

    public function testSaveOnExistingEmails(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistemail/get.json')));

        $email = $this->contactList->emails->get(36245);
        $this->assertEquals('bob@example.com', $email->address);

        $email->address = 'alice@example.com';
        $this->assertTrue($email->hasChanged('address'));

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Unable to update existing Contact List email objects');
        $email->save();
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $email = $this->contactList->emails->create();
        $email->address = 'bob@example.com';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/contactlistemail/create.json')));
        $email->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/contactlistemail/create.json'), $history[0]['request']->getBody());
    }
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlistemail/get.json')));
        $email = $this->contactList->emails->get(36245);

        $this->assertInstanceOf(Email::class, $email);
        $this->assertSame($this->contactList, $email->contactList);
        $this->assertEquals(36245, $email->id);
        $this->assertEquals('bob@example.com', $email->address);
        $this->assertTrue($email->verified);
    }
}
