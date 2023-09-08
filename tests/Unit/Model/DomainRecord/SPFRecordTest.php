<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\SPF;
use GuzzleHttp\Psr7\Response;

class SPFRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/spf.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::SPF(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(SPF::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals('v=spf1 include:example.com ?all', $record->lastValues->standard[0]->value);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(SPF::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals('v=spf1 include:example.com ?all', $record->value[0]->value);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::SPF();

        $value = new SPF();
        $value->enabled = true;
        $value->value = 'v=spf1 include:example.com ?all';
        $record->addValue($value);

        $value = new SPF();
        $value->enabled = false;
        $value->value = 'v=spf1 include:subdomain.example.com ?all';
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-spf.json'), $history[0]['request']->getBody());
    }
}
