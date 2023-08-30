<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\CAA;
use GuzzleHttp\Psr7\Response;

class CAARecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/caa.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::CAA(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(CAA::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('issue', $record->lastValues->standard[0]->tag);
        $this->assertEquals('digicert.com', $record->lastValues->standard[0]->data);
        $this->assertEquals(0, $record->lastValues->standard[0]->flags);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(CAA::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('issue', $record->value[0]->tag);
        $this->assertEquals('digicert.com', $record->value[0]->data);
        $this->assertEquals(0, $record->value[0]->flags);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::CAA();

        $value = new CAA();
        $value->tag = 'issue';
        $value->data = 'digicert.com';
        $value->flags = 0;
        $record->addValue($value);

        $value = new CAA();
        $value->enabled = false;
        $value->tag = 'issue';
        $value->data = 'letsencrypt.com';
        $value->flags = 0;
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-caa.json'), $history[0]['request']->getBody());
    }
}
