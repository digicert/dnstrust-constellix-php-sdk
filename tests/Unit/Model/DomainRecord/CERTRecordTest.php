<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\CERT;
use GuzzleHttp\Psr7\Response;

class CERTRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/cert.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::CERT(), $record->type);

        $this->assertCount(1, $record->lastValues->standard);
        $this->assertInstanceOf(CERT::class, $record->lastValues->standard[0]);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertTrue($record->lastValues->standard[0]->enabled);
        $this->assertEquals(1, $record->lastValues->standard[0]->certificateType);
        $this->assertEquals(2, $record->lastValues->standard[0]->keyTag);
        $this->assertEquals(3, $record->lastValues->standard[0]->algorithm);
        $this->assertEquals("Q2VydGlmaWNhdGUgRGF0YQ==", $record->lastValues->standard[0]->certificate);

        $this->assertCount(1, $record->value);
        $this->assertInstanceOf(CERT::class, $record->value[0]);
        $this->assertTrue($record->value[0]->enabled);
        $this->assertEquals(1, $record->value[0]->certificateType);
        $this->assertEquals(2, $record->value[0]->keyTag);
        $this->assertEquals(3, $record->value[0]->algorithm);
        $this->assertEquals("Q2VydGlmaWNhdGUgRGF0YQ==", $record->value[0]->certificate);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = '';
        $record->type = RecordType::CERT();

        $value = new CERT();
        $value->certificateType = 1;
        $value->keyTag = 2;
        $value->algorithm = 3;
        $value->certificate = base64_encode('Certificate Data');
        $record->addValue($value);

        $value = new CERT();
        $value->enabled = false;
        $value->certificateType = 5;
        $value->keyTag = 6;
        $value->algorithm = 7;
        $value->certificate = base64_encode('Certificate Data');
        $record->addValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-cert.json'), $history[0]['request']->getBody());
    }
}
