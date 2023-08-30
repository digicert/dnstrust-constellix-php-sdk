<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use GuzzleHttp\Psr7\Response;

class MXRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/mx.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::MX(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(MX::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('mail.example.com', $record->lastValues->standard[0]->server);
        $this->assertEquals(10, $record->lastValues->standard[0]->priority);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(MX::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('mail.example.com', $record->value[0]->server);
        $this->assertEquals(10, $record->value[0]->priority);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::MX();

        $value = new MX();
        $value->enabled = true;
        $value->server = 'mail.example.com';
        $value->priority = 10;
        $record->addValue($value);

        $value = new MX();
        $value->enabled = false;
        $value->server = 'mail2.example.com';
        $value->priority = 20;
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-mx.json'), $history[0]['request']->getBody());
    }
}
