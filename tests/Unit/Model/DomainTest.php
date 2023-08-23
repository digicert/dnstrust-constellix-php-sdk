<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\Helpers\SOA;
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

    // Loading data from the API

    // Persisting data to the API

    public function testNewDomainsShouldGetAnSOAObject(): void
    {
        $domain = $this->api->domains->create();
        $this->assertInstanceOf(SOA::class, $domain->soa);
    }

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

    public function testDataIsSentCorrectlyOnSave(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $domain = $this->api->domains->create();
        $domain->name = 'example.com';
        $domain->save();

        $this->assertCount(1, $history);
        $this->assertJson($this->getFixture('requests/domain/create-simple.json'), (string)$history[0]['request']->getBody());

        $domain->note = 'New Domain';

        $domain->setTemplate(1234);

        $domain->setVanityNameserver(1);
    }
}
