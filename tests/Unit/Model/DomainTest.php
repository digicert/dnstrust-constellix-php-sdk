<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\Helpers\SOA;
use Constellix\Client\Models\Tag;
use Constellix\Client\Models\Template;
use Constellix\Client\Models\VanityNameserver;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class DomainTest extends TestCase
{
    protected Client $api;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    // General Model Tests - We'll do them using the Domains model

    public function testPropertiesAreMarkedAsChanged(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $this->assertFalse($domain->hasChanged());
        $this->assertFalse($domain->hasChanged('note'));
        $this->assertFalse($domain->hasChanged('vanityNameserver'));
        $domain->note = 'Foobar';
        $this->assertTrue($domain->hasChanged());
        $this->assertTrue($domain->hasChanged('note'));
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        // Changed status should be reset to false on object being saved
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain->save();
        $this->assertFalse($domain->hasChanged());
        $this->assertFalse($domain->hasChanged('note'));
        $this->assertFalse($domain->hasChanged('vanityNameserver'));
    }

    public function testSavingIsNoopIfNothingChanged(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));

        $domain = $this->api->domains->get(366246);
        $this->assertCount(1, $history);
        $this->assertFalse($domain->hasChanged());

        $domain->save();

        $this->assertCount(1, $history);
        $this->assertFalse($domain->hasChanged());
    }

    public function testInvalidPropertyIsNull(): void
    {
        $domain = $this->api->domains->create();
        $this->assertNull($domain->foobar);
    }

    public function testLazyLoading(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366346);
        // Check we've only made one API request
        $this->assertCount(1, $history);

        // Read some properties we have of a vanity nameserver
        $this->assertInstanceOf(VanityNameserver::class, $domain->vanityNameserver);
        $this->assertEquals(82648967, $domain->vanityNameserver->id);

        // Ensure we still haven't read from the API
        $this->assertCount(1, $history);

        // Set up our data for the Vanity NS
        $this->mock->append(new Response(200, [], $this->getFixture('responses/vanitynameserver/get.json')));
        $this->assertEquals('My Vanity nameserver', $domain->vanityNameserver->name);

        // Check we made a second request to the API to get the data
        $this->assertCount(2, $history);
        $this->assertEquals('GET', $history[1]['request']->getMethod());
        $this->assertEquals('/v4/vanitynameservers/82648967', $history[1]['request']->getUri()->getPath());

        // Check we don't make a third request
        $this->assertEquals(674, $domain->vanityNameserver->nameserverGroup->id);
        $this->assertCount(2, $history);
    }

    // Tests for Sub-resource Managers
    public function testHistoryCannotBeFetchedOnNewObject(): void
    {
        $domain = $this->api->domains->create();

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Domain must be created before you can access history');
        $history = $domain->history;
    }

    public function testSnapshotsCannotBeFetchedOnNewObject(): void
    {
        $domain = $this->api->domains->create();

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Domain must be created before you can access snapshots');
        $snapshots = $domain->snapshots;
    }

    public function testRecordsCannotBeFetchedOnNewObject(): void
    {
        $domain = $this->api->domains->create();

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Domain must be created before you can access records');
        $records = $domain->records;
    }

    // Tests for referenced objects

    public function testNewDomainsShouldGetAnSOAObject(): void
    {
        $domain = $this->api->domains->create();
        $this->assertInstanceOf(SOA::class, $domain->soa);
    }

    public function testSettingTemplateToNull(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $template = $domain->template;

        $this->assertInstanceOf(Template::class, $template);
        $this->assertFalse($domain->hasChanged('template'));

        $domain->setTemplate(null);

        $this->assertNull($domain->template);
        $this->assertTrue($domain->hasChanged('template'));
    }
    public function testSettingTemplateToNullDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $template = $domain->template;

        $this->assertInstanceOf(Template::class, $template);
        $this->assertFalse($domain->hasChanged('template'));

        $domain->template = null;

        $this->assertNull($domain->template);
        $this->assertTrue($domain->hasChanged('template'));
    }

    public function testSettingTemplateToID(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->template);
        $this->assertFalse($domain->hasChanged('template'));

        $domain->setTemplate(1234);

        $this->assertInstanceOf(Template::class, $domain->template);
        $this->assertEquals(1234, $domain->template->id);
        $this->assertTrue($domain->hasChanged('template'));
    }

    public function testSettingTemplateToStdClass(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->template);
        $this->assertFalse($domain->hasChanged('template'));

        $domain->setTemplate((object)[
            'id' => 1234,
        ]);

        $this->assertInstanceOf(Template::class, $domain->template);
        $this->assertEquals(1234, $domain->template->id);
        $this->assertTrue($domain->hasChanged('template'));
    }

    public function testSettingTemplateToObject(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->template);
        $this->assertFalse($domain->hasChanged('template'));

        $template = $this->api->templates->create();
        $domain->setTemplate($template);

        $this->assertSame($template, $domain->template);
        $this->assertTrue($domain->hasChanged('template'));
    }

    public function testSettingTemplateToObjectDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->template);
        $this->assertFalse($domain->hasChanged('template'));

        $template = $this->api->templates->create();
        $domain->template = $template;

        $this->assertSame($template, $domain->template);
        $this->assertTrue($domain->hasChanged('template'));
    }

    public function testSettingVanityNameserverToNull(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $nameserver = $domain->vanityNameserver;

        $this->assertInstanceOf(VanityNameserver::class, $nameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $domain->setVanityNameserver(null);

        $this->assertNull($domain->vanityNameserver);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    public function testSettingVanityNameserverToNullDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $nameserver = $domain->vanityNameserver;

        $this->assertInstanceOf(VanityNameserver::class, $nameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $domain->vanityNameserver = null;

        $this->assertNull($domain->vanityNameserver);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    public function testSettingVanityNameserverToID(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->vanityNameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $domain->setVanityNameserver(1234);

        $this->assertInstanceOf(VanityNameserver::class, $domain->vanityNameserver);
        $this->assertEquals(1234, $domain->vanityNameserver->id);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    public function testSettingVanityNameserverToStdClass(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->vanityNameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $domain->setVanityNameserver((object)[
            'id' => 1234,
        ]);

        $this->assertInstanceOf(VanityNameserver::class, $domain->vanityNameserver);
        $this->assertEquals(1234, $domain->vanityNameserver->id);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    public function testSettingVanityNameserverToObject(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->vanityNameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $nameServer = $this->api->vanitynameservers->create();
        $domain->setVanityNameserver($nameServer);

        $this->assertSame($nameServer, $domain->vanityNameserver);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    public function testSettingVanityNameserverToObjectDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));
        $domain = $this->api->domains->get(366246);

        $this->assertNull($domain->vanityNameserver);
        $this->assertFalse($domain->hasChanged('vanityNameserver'));

        $nameServer = $this->api->vanitynameservers->create();
        $domain->vanityNameserver = $nameServer;

        $this->assertSame($nameServer, $domain->vanityNameserver);
        $this->assertTrue($domain->hasChanged('vanityNameserver'));
    }

    // Referenced collection properties

    public function testTags(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/multiple.json')));
        $tags = $this->api->tags->paginate();

        $domain = $this->api->domains->create();
        $this->assertCount(0, $domain->tags);

        $domain->addTag($tags[0]);
        $this->assertCount(1, $domain->tags);
        $this->assertSame($tags[0], $domain->tags[0]);

        // Adding the same tag shouldn't increase the amount
        $domain->addTag($tags[0]);
        $this->assertCount(1, $domain->tags);

        // Adding a new tag should add it
        $domain->addTag($tags[1]);
        $this->assertCount(2, $domain->tags);
        $this->assertSame($tags[1], $domain->tags[1]);

        // Now removing a tag should remove it
        $domain->removeTag($tags[0]);
        $this->assertCount(1, $domain->tags);

        // Removing it a second time will do nothing
        $domain->removeTag($tags[0]);
        $this->assertCount(1, $domain->tags);

        // Tags will re-index
        $this->assertSame($tags[1], $domain->tags[0]);
    }

    public function testContactLists(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/multiple.json')));
        $contacts = $this->api->contactlists->paginate();

        $domain = $this->api->domains->create();
        $this->assertCount(0, $domain->contacts);

        $domain->addContactList($contacts[0]);
        $this->assertCount(1, $domain->contacts);
        $this->assertSame($contacts[0], $domain->contacts[0]);

        // Adding the same contact list shouldn't increase the amount
        $domain->addContactList($contacts[0]);
        $this->assertCount(1, $domain->contacts);

        // Adding a new contact list should add it
        $domain->addContactList($contacts[1]);
        $this->assertCount(2, $domain->contacts);
        $this->assertSame($contacts[1], $domain->contacts[1]);

        // Now removing a contact list should remove it
        $domain->removeContactList($contacts[0]);
        $this->assertCount(1, $domain->contacts);

        // Removing it a second time will do nothing
        $domain->removeContactList($contacts[0]);
        $this->assertCount(1, $domain->contacts);

        // Contact lists will re-index
        $this->assertSame($contacts[1], $domain->contacts[0]);
    }


    // General Tests for Domains

    public function testSaveOnNewDomains(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/domain/create.json')));

        $domain = $this->api->domains->create();
        $domain->name = 'example.com';
        $domain->note = 'My New Domain';

        $this->assertNull($domain->id);

        $domain->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains', $request->getUri()->getPath());

        $this->assertEquals(366246, $domain->id);
    }

    public function testSaveOnExistingDomains(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/noreferences.json')));

        $domain = $this->api->domains->get(366246);
        $this->assertNull($domain->template);

        $domain->setTemplate(83675283);
        $this->assertTrue($domain->hasChanged('template'));

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/domains/366246', $request->getUri()->getPath());


        // Check we have loaded the new properties from the changed domain
        $this->assertFalse($domain->hasChanged('template'));
        $this->assertInstanceOf(Template::class, $domain->template);
        $this->assertEquals(83675283, $domain->template->id);
    }

    // Loading data from the API

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);

        // Test everything on our domain is as expected
        $this->assertEquals(366246, $domain->id);
        $this->assertEquals('example.com', $domain->name);
        $this->assertEquals('My Domain', $domain->note);
        $this->assertEquals('ACTIVE', $domain->status);
        $this->assertTrue($domain->enabled);
        $this->assertTrue($domain->geoip);
        $this->assertTrue($domain->gtd);

        // SOA - This is a helper class, so no real separate tests are needed
        $this->assertInstanceOf(SOA::class, $domain->soa);
        $this->assertEquals('ns11.constellix.com', $domain->soa->primaryNameserver);
        $this->assertEquals('admin.example.com', $domain->soa->email);
        $this->assertEquals(86400, $domain->soa->ttl);
        $this->assertEquals(2020061601, $domain->soa->serial);
        $this->assertEquals(86400, $domain->soa->refresh);
        $this->assertEquals(7200, $domain->soa->retry);
        $this->assertEquals(3600000, $domain->soa->expire);
        $this->assertEquals(180, $domain->soa->negativeCache);

        // Nameservers
        $this->assertCount(3, $domain->nameservers);
        $this->assertEquals('ns11.constellix.com', $domain->nameservers[0]);
        $this->assertEquals('ns21.constellix.com', $domain->nameservers[1]);
        $this->assertEquals('ns31.constellix.com', $domain->nameservers[2]);

        // We'll test collection properties contain basic information, but testing the objects themselves is done in
        // their own tests. As long as we have the ID, we're OK.
        $this->assertCount(1, $domain->contacts);
        $this->assertInstanceOf(ContactList::class, $domain->contacts[0]);
        $this->assertEquals(2668228, $domain->contacts[0]->id);

        $this->assertCount(1, $domain->tags);
        $this->assertInstanceOf(Tag::class, $domain->tags[0]);
        $this->assertEquals(824, $domain->tags[0]->id);

        // Similar for referenced objects - as long as we have the object and the ID, that's all we need
        $this->assertInstanceOf(Template::class, $domain->template);
        $this->assertEquals(83675283, $domain->template->id);

        $this->assertInstanceOf(VanityNameserver::class, $domain->vanityNameserver);
        $this->assertEquals(82648967, $domain->vanityNameserver->id);

        $this->assertInstanceOf(\DateTime::class, $domain->createdAt);
        $this->assertEquals('2019-08-24T14:15:22+00:00', $domain->createdAt->format('c'));

        $this->assertInstanceOf(\DateTime::class, $domain->updatedAt);
        $this->assertEquals('2019-07-24T14:15:22+00:00', $domain->updatedAt->format('c'));
    }

    // Persisting data to the API

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/domain/create.json')));
        $domain = $this->api->domains->create();
        $domain->name = 'example.com';
        $domain->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domain/create-simple.json'), (string)$history[0]['request']->getBody());

        $domain2 = $this->api->domains->create();

        $domain2->name = 'domain2.example.com';
        $domain2->note = 'New Domain';
        $domain2->geoip = true;
        $domain2->gtd = true;

        $soa = new SOA();
        $soa->primaryNameserver = 'ns11.constellix.com';
        $soa->email = 'admin.domain2.example.com';
        $soa->ttl = 86400;
        $soa->refresh = 86400;
        $soa->expire = 3600000;
        $soa->negativeCache = 180;
        $domain2->soa = $soa;

        $domain2->setTemplate(1234);
        $domain2->setVanityNameserver(1);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/multiple.json')));
        $contactLists = $this->api->contactlists->paginate();

        $domain2->addContactList($contactLists[0]);
        $domain2->addContactList($contactLists[1]);


        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/multiple.json')));
        $tags = $this->api->tags->paginate();

        $domain2->addTag($tags[0]);
        $domain2->addTag($tags[1]);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domain/create.json')));
        $domain2->save();

        $this->assertCount(4, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domain/create-complex.json'), (string)$history[3]['request']->getBody());
    }

    public function testToString(): void
    {
        $domain = $this->api->domains->create();
        $this->assertEquals('Domain:#', (string)$domain);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->get(366246);
        $this->assertEquals('Domain:366246', (string)$domain);
    }
}
