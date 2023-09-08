<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use Constellix\Client\Models\Helpers\RecordValues\NS;
use GuzzleHttp\Psr7\Response;

class NSRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/ns.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::NS(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(NS::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('ns1.example.com', $record->lastValues->standard[0]->host);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(NS::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('ns1.example.com', $record->value[0]->host);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::NS();

        $value = new NS();
        $value->enabled = true;
        $value->host = 'ns1.example.com';
        $record->addValue($value);

        $value = new NS();
        $value->enabled = false;
        $value->host = 'ns2.example.com';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-ns.json'), $history[0]['request']->getBody());
    }
}
