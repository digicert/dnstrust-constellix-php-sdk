<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\HINFO;
use GuzzleHttp\Psr7\Response;

class HINFORecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/hinfo.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::HINFO(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(HINFO::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('Linux', $record->lastValues->standard[0]->os);
        $this->assertEquals("x86", $record->lastValues->standard[0]->cpu);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(HINFO::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('Linux', $record->value[0]->os);
        $this->assertEquals("x86", $record->value[0]->cpu);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::HINFO();

        $value = new HINFO();
        $value->enabled = true;
        $value->os = 'Linux';
        $value->cpu = 'x86';
        $record->addValue($value);

        $value = new HINFO();
        $value->enabled = false;
        $value->os = 'Windows';
        $value->cpu = 'x64';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-hinfo.json'), $history[0]['request']->getBody());
    }
}
