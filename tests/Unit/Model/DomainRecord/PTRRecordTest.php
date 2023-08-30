<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use Constellix\Client\Models\Helpers\RecordValues\NS;
use Constellix\Client\Models\Helpers\RecordValues\PTR;
use GuzzleHttp\Psr7\Response;

class PTRRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/ptr.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::PTR(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(PTR::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('system.example.com', $record->lastValues->standard[0]->system);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(PTR::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('system.example.com', $record->value[0]->system);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '127';
        $record->type = RecordType::PTR();

        $value = new PTR();
        $value->enabled = true;
        $value->system = 'system1.example.com';
        $record->addValue($value);

        $value = new PTR();
        $value->enabled = false;
        $value->system = 'system2.example.com';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-ptr.json'), $history[0]['request']->getBody());
    }
}
