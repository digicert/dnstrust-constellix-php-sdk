<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Enums\Pools\ITOHandicapFactor;
use Constellix\Client\Enums\Pools\ITORegion;
use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Enums\Pools\PoolValuePolicy;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\Helpers\ITO;
use Constellix\Client\Models\Helpers\ITOConfig;
use Constellix\Client\Models\PoolValue;
use Constellix\Client\Models\Template;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class PoolTest extends TestCase
{
    protected Client $api;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testNewPoolGetsITOObject(): void
    {
        $pool = $this->api->pools->create();
        $this->assertInstanceOf(ITO::class, $pool->ito);
    }

    // Referenced Collections
    public function testContactLists(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/multiple.json')));
        $contacts = $this->api->contactlists->paginate();

        $pool = $this->api->pools->create();
        $this->assertCount(0, $pool->contacts);

        $pool->addContactList($contacts[0]);
        $this->assertCount(1, $pool->contacts);
        $this->assertSame($contacts[0], $pool->contacts[0]);

        // Adding the same contact list shouldn't increase the amount
        $pool->addContactList($contacts[0]);
        $this->assertCount(1, $pool->contacts);

        // Adding a new contact list should add it
        $pool->addContactList($contacts[1]);
        $this->assertCount(2, $pool->contacts);
        $this->assertSame($contacts[1], $pool->contacts[1]);

        // Now removing a contact list should remove it
        $pool->removeContactList($contacts[0]);
        $this->assertCount(1, $pool->contacts);

        // Removing it a second time will do nothing
        $pool->removeContactList($contacts[0]);
        $this->assertCount(1, $pool->contacts);

        // Contact lists will re-index
        $this->assertSame($contacts[1], $pool->contacts[0]);
    }

    public function testSaveOnNewPool(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/pool/create.json')));

        $pool = $this->api->pools->create();
        $pool->name = 'My Pool';
        $pool->type = PoolType::A();

        $this->assertNull($pool->id);

        $pool->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/pools', $request->getUri()->getPath());

        $this->assertEquals(7665, $pool->id);
    }

    public function testSaveOnExistingPool(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/noreferences.json')));

        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertEquals('My Pool', $pool->name);
        $this->assertCount(0, $pool->values);

        $pool->name = 'New Name';
        $this->assertTrue($pool->hasChanged('name'));

        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $pool->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/pools/a/7665', $request->getUri()->getPath());


        // Check we have loaded the new properties from the changed domain
        $this->assertFalse($pool->hasChanged('values'));
        $this->assertCount(1, $pool->values);
    }

    public function testSettingTypeAsString(): void
    {
        $pool = $this->api->pools->create();
        $this->assertNull($pool->type);
        $pool->setType('A');
        $this->assertInstanceOf(PoolType::class, $pool->type);
        $this->assertEquals(PoolType::A(), $pool->type);
    }

    public function testSettingTypeAsEnum(): void
    {
        $pool = $this->api->pools->create();
        $this->assertNull($pool->type);
        $pool->setType(PoolType::A());
        $this->assertInstanceOf(PoolType::class, $pool->type);
        $this->assertEquals(PoolType::A(), $pool->type);
    }

    public function testSettingTypeDirectly(): void
    {
        $pool = $this->api->pools->create();
        $this->assertNull($pool->type);
        $pool->type = PoolType::A();
        $this->assertInstanceOf(PoolType::class, $pool->type);
        $this->assertEquals(PoolType::A(), $pool->type);
    }

    public function testUnableToSetTypeOnExistingPool(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertEquals(PoolType::A(), $pool->type);

        $this->expectException(ReadOnlyPropertyException::class);
        $this->expectExceptionMessage('Unable to set type after a Pool has been created');
        $pool->type = PoolType::CNAME();
    }

    public function testSettingValues(): void
    {
        $pool = $this->api->pools->create();
        $this->assertCount(0, $pool->values);
        $value = $pool->createValue('127.0.0.1');
        $this->assertInstanceOf(PoolValue::class, $value);
        $this->assertCount(1, $pool->values);
        $this->assertSame($value, $pool->values[0]);
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $pool = $this->api->pools->get(PoolType::A(), 7665);

        $this->assertEquals(7665, $pool->id);
        $this->assertInstanceOf(PoolType::class, $pool->type);
        $this->assertEquals(PoolType::A(), $pool->type);
        $this->assertEquals(1, $pool->return);
        $this->assertEquals(1, $pool->minimumFailover);
        $this->assertFalse($pool->failed);
        $this->assertTrue($pool->enabled);

        $this->assertInstanceOf(ITO::class, $pool->ito);
        $this->assertTrue($pool->ito->enabled);
        $this->assertInstanceOf(ITOConfig::class, $pool->ito->config);
        $this->assertEquals(60, $pool->ito->config->period);
        $this->assertEquals(1, $pool->ito->config->maximumNumberOfResults);
        $this->assertEquals(90, $pool->ito->config->deviationAllowance);
        $this->assertEquals(ITORegion::WORLD(), $pool->ito->config->monitoringRegion);
        $this->assertEquals(ITOHandicapFactor::PERCENT(), $pool->ito->config->handicapFactor);

        $this->assertCount(1, $pool->values);
        $value = $pool->values[0];
        $this->assertInstanceOf(PoolValue::class, $value);
        $this->assertEquals('198.51.100.42', $value->value);
        $this->assertEquals(1000, $value->weight);
        $this->assertTrue($value->enabled);
        $this->assertTrue($value->activated);
        $this->assertTrue($value->failed);
        $this->assertEquals(5.2, $value->speed);
        $this->assertEquals(5, $value->handicap);
        $this->assertInstanceOf(PoolValuePolicy::class, $value->policy);
        $this->assertEquals(PoolValuePolicy::FOLLOW_SONAR(), $value->policy);
        $this->assertEquals(76627, $value->sonarCheckId);

        // Referenced objects, we only care if we have the right type and ID, everything else is not our concern
        $this->assertCount(1, $pool->contacts);
        $this->assertInstanceOf(ContactList::class, $pool->contacts[0]);
        $this->assertEquals(2668228, $pool->contacts[0]->id);

        $this->assertCount(1, $pool->domains);
        $this->assertInstanceOf(Domain::class, $pool->domains[0]);
        $this->assertEquals(366246, $pool->domains[0]->id);

        $this->assertCount(1, $pool->templates);
        $this->assertInstanceOf(Template::class, $pool->templates[0]);
        $this->assertEquals(83675283, $pool->templates[0]->id);
    }

    public function testExistingPoolWithNoITOConfig(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/noreferences.json')));
        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertInstanceOf(ITOConfig::class, $pool->ito->config);
    }
    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $pool = $this->api->pools->create();
        $pool->type = PoolType::A();
        $pool->name = 'My Pool';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/pool/create.json')));
        $pool->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/pool/create-simple.json'), $history[0]['request']->getBody());

        $pool2 = $this->api->pools->create();
        $pool2->type = PoolType::A();
        $pool2->name = 'My Pool';
        $pool2->minimumFailover = 1;
        $pool2->return = 1;
        $pool2->enabled = true;

        $value = $pool2->createValue('198.51.100.42');
        $value->weight = 1000;
        $value->handicap = 10;
        $value->policy = PoolValuePolicy::FOLLOW_SONAR();
        $value->sonarCheckId = 76627;

        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/multiple.json')));
        $contacts = $this->api->contactlists->paginate();
        $pool2->addContactList($contacts[0]);
        $pool2->addContactList($contacts[1]);

        $pool2->ito->enabled = true;
        $pool2->ito->config->deviationAllowance = 90;
        $pool2->ito->config->maximumNumberOfResults = 1;
        $pool2->ito->config->handicapFactor = ITOHandicapFactor::PERCENT();
        $pool2->ito->config->period = 60;

        $this->mock->append(new Response(201, [], $this->getFixture('responses/pool/create.json')));
        $pool2->save();

        $this->assertCount(3, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/pool/create-complex.json'), $history[2]['request']->getBody());
    }

    public function testToString(): void
    {
        $pool = $this->api->pools->create();
        $this->assertEquals('Pool:#', (string)$pool);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/pool/get.json')));
        $pool = $this->api->pools->get(PoolType::A(), 7665);
        $this->assertEquals('Pool:7665', (string)$pool);
    }
}
