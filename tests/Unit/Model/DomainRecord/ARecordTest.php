<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\GTDLocation;
use Constellix\Client\Enums\Records\FailoverMode;
use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Models\GeoProximity;
use Constellix\Client\Models\Helpers\RecordValues\Failover;
use Constellix\Client\Models\Helpers\RecordValues\FailoverValue;
use Constellix\Client\Models\Helpers\RecordValues\RoundRobinFailover;
use Constellix\Client\Models\Helpers\RecordValues\Standard;
use Constellix\Client\Models\IPFilter;
use Constellix\Client\Models\Pool;
use Constellix\Client\Models\Record;
use GuzzleHttp\Psr7\Response;

class ARecordTest extends RecordTestCase
{
    // General Record Tests - Valid for all records, we'll do them here
    public function testUnableToSetTypeOnExistingRecords(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);

        $this->expectException(ReadOnlyPropertyException::class);
        $this->expectExceptionMessage('Unable to set type on a record that has been created');

        $record->type = RecordType::CNAME();
    }

    public function testContactLists(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/multiple.json')));
        $contacts = $this->api->contactlists->paginate();

        $record = $this->domain->records->create();
        $this->assertCount(0, $record->contacts);

        $record->addContactList($contacts[0]);
        $this->assertCount(1, $record->contacts);
        $this->assertSame($contacts[0], $record->contacts[0]);

        // Adding the same contact list shouldn't increase the amount
        $record->addContactList($contacts[0]);
        $this->assertCount(1, $record->contacts);

        // Adding a new contact list should add it
        $record->addContactList($contacts[1]);
        $this->assertCount(2, $record->contacts);
        $this->assertSame($contacts[1], $record->contacts[1]);

        // Now removing a contact list should remove it
        $record->removeContactList($contacts[0]);
        $this->assertCount(1, $record->contacts);

        // Removing it a second time will do nothing
        $record->removeContactList($contacts[0]);
        $this->assertCount(1, $record->contacts);

        // Contact lists will re-index
        $this->assertSame($contacts[1], $record->contacts[0]);
    }

    public function testSettingIPFilterToNull(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);
        $ipfilter = $record->ipfilter;

        $this->assertInstanceOf(IPFilter::class, $ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));

        $record->setIPFilter(null);

        $this->assertNull($record->ipfilter);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }
    public function testSettingIPFilterToNullDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);
        $ipfilter = $record->ipfilter;

        $this->assertInstanceOf(IPFilter::class, $ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));

        $record->ipfilter = null;

        $this->assertNull($record->ipfilter);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }

    public function testSettingIPFilterToID(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));

        $record->setIPFilter(1234);

        $this->assertInstanceOf(IPFilter::class, $record->ipfilter);
        $this->assertEquals(1234, $record->ipfilter->id);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }

    public function testSettingIPFilterToStdClass(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));

        $record->setIPFilter((object)[
            'id' => 1234,
        ]);

        $this->assertInstanceOf(IPFilter::class, $record->ipfilter);
        $this->assertEquals(1234, $record->ipfilter->id);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }

    public function testSettingIPFilterToObject(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));

        $filter = $this->api->ipfilters->create();
        $record->setIPFilter($filter);

        $this->assertSame($filter, $record->ipfilter);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }

    public function testSettingIPFilterToObjectDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->ipfilter);
        $this->assertFalse($record->hasChanged('ipfilter'));
        ;

        $filter = $this->api->ipfilters->create();
        $record->ipfilter = $filter;

        $this->assertSame($filter, $record->ipfilter);
        $this->assertTrue($record->hasChanged('ipfilter'));
    }

    public function testSettingGeoProximityToNull(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);
        $geoproximity = $record->geoproximity;

        $this->assertInstanceOf(GeoProximity::class, $geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));

        $record->setGeoProximity(null);

        $this->assertNull($record->geoproximity);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }
    public function testSettingGeoProximityToNullDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);
        $geoproximity = $record->geoproximity;

        $this->assertInstanceOf(GeoProximity::class, $geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));

        $record->geoproximity = null;

        $this->assertNull($record->geoproximity);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }

    public function testSettingGeoProximityToID(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));

        $record->setGeoProximity(1234);

        $this->assertInstanceOf(GeoProximity::class, $record->geoproximity);
        $this->assertEquals(1234, $record->geoproximity->id);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }

    public function testSettingGeoProximityToStdClass(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));

        $record->setGeoProximity((object)[
            'id' => 1234,
        ]);

        $this->assertInstanceOf(GeoProximity::class, $record->geoproximity);
        $this->assertEquals(1234, $record->geoproximity->id);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }

    public function testSettingGeoProximityToObject(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));

        $geoproximity = $this->api->geoproximity->create();
        $record->setGeoProximity($geoproximity);

        $this->assertSame($geoproximity, $record->geoproximity);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }

    public function testSettingGeoProximityToObjectDirectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));
        $record = $this->domain->records->get(732673);

        $this->assertNull($record->geoproximity);
        $this->assertFalse($record->hasChanged('geoproximity'));
        ;

        $geoproximity = $this->api->geoproximity->create();
        $record->geoproximity = $geoproximity;

        $this->assertSame($geoproximity, $record->geoproximity);
        $this->assertTrue($record->hasChanged('geoproximity'));
    }

    // Domain Record Specific Tests
    public function testSaveOnNewRecords(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));

        $record = $this->domain->records->create();
        $record->name = 'www';

        $this->assertNull($record->id);

        $record->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/records', $request->getUri()->getPath());

        $this->assertEquals(732673, $record->id);
    }

    public function testSaveOnExistingRecords(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-noreferences.json')));

        $record = $this->domain->records->get(732673);
        $this->assertNull($record->ipfilter);

        $record->setIPFilter(47345837);
        $this->assertTrue($record->hasChanged('ipfilter'));

        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/domains/366246/records/732673', $request->getUri()->getPath());

        $this->assertFalse($record->hasChanged('ipfilter'));
        $this->assertInstanceOf(IPFilter::class, $record->ipfilter);
        $this->assertEquals(47345837, $record->ipfilter->id);
    }

    // Standard Record Type Parsing - Most of the testing of parsing data is done on this record
    // subsequent tests will focus on just the value and mode

    public function testStandardApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a.json')));
        $record = $this->domain->records->get(732673);

        $this->assertInstanceOf(Record::class, $record);
        $this->assertSame($this->domain, $record->domain);

        $this->assertEquals(732673, $record->id);
        $this->assertEquals(RecordType::A(), $record->type);
        $this->assertEquals(3600, $record->ttl);
        $this->assertEquals('www', $record->name);
        $this->assertEquals(RecordMode::STANDARD(), $record->mode);
        $this->assertTrue($record->ipfilterDrop);
        $this->assertTrue($record->geoFailover);
        $this->assertEquals('This is my DNS record', $record->notes);
        $this->assertNull($record->skipLookup);

        $this->assertInstanceOf(GTDLocation::class, $record->region);
        $this->assertEquals(GTDLocation::DEFAULT(), $record->region);

        $this->assertInstanceOf(IPFilter::class, $record->ipfilter);
        $this->assertEquals(47345837, $record->ipfilter->id);

        $this->assertInstanceOf(GeoProximity::class, $record->geoproximity);
        $this->assertEquals(4367769, $record->geoproximity->id);

        $this->assertCount(1, $record->contacts);
        $this->assertInstanceOf(ContactList::class, $record->contacts[0]);
        $this->assertEquals(2668228, $record->contacts[0]->id);

        // Our standard record value
        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(Standard::class, $record->value[0]);
        $this->assertEquals('198.51.100.42', $record->value[0]->value);
        $this->assertTrue($record->value[0]->enabled);

        // Previous values for this record for different types
        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(Standard::class, $record->lastValues->standard[0]);
        $this->assertEquals('198.51.100.42', $record->lastValues->standard[0]->value);
        $this->assertTrue($record->lastValues->standard[0]->enabled);

        $this->assertInstanceOf(Failover::class, $record->lastValues->failover);
        $this->assertInstanceOf(FailoverMode::class, $record->lastValues->failover->mode);
        $this->assertEquals(FailoverMode::NORMAL(), $record->lastValues->failover->mode);
        $this->assertCount(1, $record->lastValues->failover->values);
        $this->assertInstanceOf(FailoverValue::class, $record->lastValues->failover->values[0]);
        $this->assertTrue($record->lastValues->failover->values[0]->enabled);
        $this->assertEquals(1, $record->lastValues->failover->values[0]->order);
        $this->assertEquals(76627, $record->lastValues->failover->values[0]->sonarCheckId);
        $this->assertTrue($record->lastValues->failover->values[0]->active);
        $this->assertFalse($record->lastValues->failover->values[0]->failed);
        $this->assertEquals('N/A', $record->lastValues->failover->values[0]->status);
        $this->assertEquals('198.51.100.42', $record->lastValues->failover->values[0]->value);

        $this->assertCount(1, $record->lastValues->roundRobinFailover);
        $this->assertInstanceOf(RoundRobinFailover::class, $record->lastValues->roundRobinFailover[0]);
        $this->assertTrue($record->lastValues->roundRobinFailover[0]->enabled);
        $this->assertEquals(1, $record->lastValues->roundRobinFailover[0]->order);
        $this->assertEquals(76627, $record->lastValues->roundRobinFailover[0]->sonarCheckId);
        $this->assertTrue($record->lastValues->roundRobinFailover[0]->active);
        $this->assertFalse($record->lastValues->roundRobinFailover[0]->failed);
        $this->assertEquals('N/A', $record->lastValues->roundRobinFailover[0]->status);
        $this->assertEquals('198.51.100.42', $record->lastValues->roundRobinFailover[0]->value);

        $this->assertCount(1, $record->lastValues->pools);
        $this->assertInstanceOf(\Constellix\Client\Models\Helpers\RecordValues\Pool::class, $record->lastValues->pools[0]);
        $this->assertInstanceOf(Pool::class, $record->lastValues->pools[0]->pool);
        $this->assertEquals(7665, $record->lastValues->pools[0]->pool->id);
    }

    public function testStandardDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new Standard();
        $value->value = '127.0.0.1';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-standard-simple.json'), $history[0]['request']->getBody());

        $record2 = $this->domain->records->create();

        $record2->name = 'www';
        $record2->ttl = 60;
        $record2->type = RecordType::A();
        $record2->mode = RecordMode::STANDARD();
        $record2->notes = 'This is my A record';
        $record2->skipLookup = true;
        $record2->ipfilterDrop = true;
        $record2->geoFailover = true;
        $record2->enabled = true;
        $record2->region = GTDLocation::EUROPE();

        $this->mock->append(new Response(200, [], $this->getFixture('responses/contactlist/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/ipfilter/get.json')));
        $this->mock->append(new Response(200, [], $this->getFixture('responses/geoproximity/get.json')));

        $record2->contacts = [
            $this->api->contactlists->get(2668228)
        ];
        $record2->ipfilter = $this->api->ipfilters->get(47345837);
        $record2->geoproximity = $this->api->geoproximity->get(4367769);

        $value1 = new Standard();
        $value1->value = '198.51.100.42';
        $record2->addValue($value1);

        $value2 = new Standard();
        $value2->value = '198.51.100.43';
        $value2->enabled = false;
        $record2->addValue($value2);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record2->save();
        $this->assertCount(5, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-standard-complex.json'), $history[4]['request']->getBody());
    }

    // Failover

    public function testFailoverApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-failover.json')));
        $record = $this->domain->records->get(732673);

        $this->assertEquals(RecordMode::FAILOVER(), $record->mode);

        $this->assertInstanceOf(Failover::class, $record->value);
        $this->assertInstanceOf(FailoverMode::class, $record->value->mode);
        $this->assertEquals(FailoverMode::NORMAL(), $record->value->mode);
        $this->assertCount(1, $record->value->values);
        $this->assertInstanceOf(FailoverValue::class, $record->value->values[0]);
        $this->assertTrue($record->value->values[0]->enabled);
        $this->assertEquals(1, $record->value->values[0]->order);
        $this->assertEquals(76627, $record->value->values[0]->sonarCheckId);
        $this->assertTrue($record->value->values[0]->active);
        $this->assertFalse($record->value->values[0]->failed);
        $this->assertEquals('N/A', $record->value->values[0]->status);
        $this->assertEquals('198.51.100.42', $record->value->values[0]->value);
    }

    public function testFailoverDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new Failover();
        $value->mode = FailoverMode::NORMAL();

        $failoverValue = new FailoverValue();
        $failoverValue->enabled = true;
        $failoverValue->order = 1;
        $failoverValue->sonarCheckId = 76627;
        $failoverValue->value = '198.51.100.42';
        $value->addValue($failoverValue);

        $record->value = $value;
        $this->assertEquals(RecordMode::FAILOVER(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-failover-simple.json'), $history[0]['request']->getBody());
    }

    public function testComplexFailoverDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new Failover();
        $value->mode = FailoverMode::ONE_WAY();

        $failoverValue = new FailoverValue();
        $failoverValue->enabled = true;
        $failoverValue->order = 1;
        $failoverValue->sonarCheckId = 76627;
        $failoverValue->value = '198.51.100.42';
        $value->addValue($failoverValue);

        $failoverValue = new FailoverValue();
        $failoverValue->enabled = false;
        $failoverValue->order = 2;
        $failoverValue->sonarCheckId = 76628;
        $failoverValue->value = '198.51.100.43';
        $value->addValue($failoverValue);

        $record->value = $value;
        $this->assertEquals(RecordMode::FAILOVER(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-failover-complex.json'), $history[0]['request']->getBody());
    }

    // Round Robin Failover

    public function testRoundRobinFailoverApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-roundrobin.json')));
        $record = $this->domain->records->get(732673);

        $this->assertEquals(RecordMode::ROUNDROBINFAILOVER(), $record->mode);


        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(RoundRobinFailover::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals(1, $record->value[0]->order);
        $this->assertEquals(76627, $record->value[0]->sonarCheckId);
        $this->assertTrue($record->value[0]->active);
        $this->assertFalse($record->value[0]->failed);
        $this->assertEquals('N/A', $record->value[0]->status);
        $this->assertEquals('198.51.100.42', $record->value[0]->value);
    }

    public function testRoundRobinFailoverDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new RoundRobinFailover();
        $value->enabled = true;
        $value->order = 1;
        $value->sonarCheckId = 76627;
        $value->value = '198.51.100.42';

        $record->addValue($value);
        $this->assertEquals(RecordMode::ROUNDROBINFAILOVER(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-roundrobin-simple.json'), $history[0]['request']->getBody());
    }

    public function testRoundRobinFailoverCompldexDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new RoundRobinFailover();
        $value->enabled = true;
        $value->order = 1;
        $value->sonarCheckId = 76627;
        $value->value = '198.51.100.42';
        $record->addValue($value);

        $value = new RoundRobinFailover();
        $value->enabled = false;
        $value->order = 2;
        $value->sonarCheckId = 76628;
        $value->value = '198.51.100.43';
        $record->addValue($value);


        $this->assertEquals(RecordMode::ROUNDROBINFAILOVER(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-roundrobin-complex.json'), $history[0]['request']->getBody());
    }

    // Pools

    public function testPoolsApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/a-pools.json')));
        $record = $this->domain->records->get(732673);

        $this->assertEquals(RecordMode::POOLS(), $record->mode);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(\Constellix\Client\Models\Helpers\RecordValues\Pool::class, $record->value[0]);
        $this->assertInstanceOf(Pool::class, $record->value[0]->pool);
        $this->assertEquals(7665, $record->value[0]->pool->id);
    }

    public function testPoolDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new \Constellix\Client\Models\Helpers\RecordValues\Pool();
        $value->pool = new Pool($this->api->pools, $this->api, (object)['id' => 7665]);
        $record->addValue($value);

        $this->assertEquals(RecordMode::POOLS(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-pools-simple.json'), $history[0]['request']->getBody());
    }

    public function testPoolComplexDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::A();

        $value = new \Constellix\Client\Models\Helpers\RecordValues\Pool();
        $value->pool = new Pool($this->api->pools, $this->api, (object)['id' => 7665]);
        $record->addValue($value);

        $value = new \Constellix\Client\Models\Helpers\RecordValues\Pool();
        $value->pool = new Pool($this->api->pools, $this->api, (object)['id' => 7666]);
        $record->addValue($value);

        $this->assertEquals(RecordMode::POOLS(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-a-pools-complex.json'), $history[0]['request']->getBody());
    }
}
