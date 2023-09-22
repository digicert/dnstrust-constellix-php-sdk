<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\SRV;
use GuzzleHttp\Psr7\Response;

class SRVRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/srv.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::SRV(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(SRV::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals(10, $record->lastValues->standard[0]->weight);
        $this->assertEquals(0, $record->lastValues->standard[0]->priority);
        $this->assertEquals(5060, $record->lastValues->standard[0]->port);
        $this->assertEquals('sip.example.com.', $record->lastValues->standard[0]->host);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(SRV::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals(10, $record->value[0]->weight);
        $this->assertEquals(0, $record->value[0]->priority);
        $this->assertEquals(5060, $record->value[0]->port);
        $this->assertEquals('sip.example.com.', $record->value[0]->host);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::SRV();

        $value = new SRV();
        $value->enabled = true;
        $value->weight = 10;
        $value->priority = 0;
        $value->port = 5060;
        $value->host = 'sip.example.com.';
        $record->addValue($value);

        $value = new SRV();
        $value->enabled = false;
        $value->weight = 20;
        $value->priority = 10;
        $value->port = 34197;
        $value->host = 'factorio.example.com.';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-srv.json'), $history[0]['request']->getBody());
    }
}
