<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use Constellix\Client\Models\Helpers\RecordValues\NS;
use Constellix\Client\Models\Helpers\RecordValues\TXT;
use GuzzleHttp\Psr7\Response;

class TXTRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/txt.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::TXT(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(TXT::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('My text record', $record->lastValues->standard[0]->value);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(TXT::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('My text record', $record->value[0]->value);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::TXT();

        $value = new TXT();
        $value->enabled = true;
        $value->value = 'My text record';
        $record->addValue($value);

        $value = new TXT();
        $value->enabled = false;
        $value->value = 'GNU Terry Pratchett';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-txt.json'), $history[0]['request']->getBody());
    }
}
