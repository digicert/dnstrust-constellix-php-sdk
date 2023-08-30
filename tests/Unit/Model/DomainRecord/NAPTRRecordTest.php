<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\NAPTR;
use GuzzleHttp\Psr7\Response;

class NAPTRRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/naptr.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::NAPTR(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(NAPTR::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals(100, $record->lastValues->standard[0]->order);
        $this->assertEquals(10, $record->lastValues->standard[0]->preference);
        $this->assertEquals('S', $record->lastValues->standard[0]->flags);
        $this->assertEquals('SIP+D2U', $record->lastValues->standard[0]->service);
        $this->assertEquals('!^.*$!sip:customer-service@example.com!', $record->lastValues->standard[0]->regularExpression);
        $this->assertEquals('_sip._udp.example.com.', $record->lastValues->standard[0]->replacement);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(NAPTR::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals(100, $record->value[0]->order);
        $this->assertEquals(10, $record->value[0]->preference);
        $this->assertEquals('S', $record->value[0]->flags);
        $this->assertEquals('SIP+D2U', $record->value[0]->service);
        $this->assertEquals('!^.*$!sip:customer-service@example.com!', $record->value[0]->regularExpression);
        $this->assertEquals('_sip._udp.example.com.', $record->value[0]->replacement);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::NAPTR();

        $value = new NAPTR();
        $value->enabled = true;
        $value->order = 100;
        $value->preference = 10;
        $value->flags = 'S';
        $value->service = 'SIP+D2U';
        $value->regularExpression = '!^.*$!sip:customer-service@example.com!';
        $value->replacement = '_sip._udp.example.com.';
        $record->addValue($value);

        $value = new NAPTR();
        $value->enabled = false;
        $value->order = 120;
        $value->preference = 50;
        $value->flags = 'S';
        $value->service = 'SIP+D2U';
        $value->regularExpression = '!^.*$!sip:users@example.com!';
        $value->replacement = '_sip._udp.example.com.';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-naptr.json'), $history[0]['request']->getBody());
    }
}
