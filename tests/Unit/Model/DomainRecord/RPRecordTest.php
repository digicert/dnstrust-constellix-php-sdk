<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use Constellix\Client\Models\Helpers\RecordValues\NS;
use Constellix\Client\Models\Helpers\RecordValues\RP;
use GuzzleHttp\Psr7\Response;

class RPRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/rp.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::RP(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(RP::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('admin.example.com', $record->lastValues->standard[0]->mailbox);
        $this->assertEquals('admin.example.com', $record->lastValues->standard[0]->txt);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(RP::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('admin.example.com', $record->value[0]->mailbox);
        $this->assertEquals('admin.example.com', $record->value[0]->txt);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::RP();

        $value = new RP();
        $value->enabled = true;
        $value->mailbox = 'admin.example.com';
        $value->txt = 'admin.example.com';
        $record->addValue($value);

        $value = new RP();
        $value->enabled = false;
        $value->mailbox = 'sysadmin.example.com';
        $value->txt = 'sysadmin.example.com';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-rp.json'), $history[0]['request']->getBody());
    }
}
