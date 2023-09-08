<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Models\Helpers\RecordValues\HttpRedirection;
use GuzzleHttp\Psr7\Response;

class HttpRedirectionRecordTest extends RecordTestCase
{
    // We only care about the record type
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domainrecord/httpredirection.json')));
        $record = $this->domain->records->get(732673);
        $this->assertEquals(RecordType::HTTP(), $record->type);

        $this->assertInstanceOf(HttpRedirection::class, $record->lastValues->standard);
        $this->assertFalse($record->lastValues->standard->hard);
        $this->assertEquals(302, $record->lastValues->standard->redirectType);
        $this->assertEquals('My Website', $record->lastValues->standard->title);
        $this->assertEquals('A website containing example data', $record->lastValues->standard->description);
        $this->assertEquals('Example', $record->lastValues->standard->keywords);
        $this->assertEquals('https://www.example.com', $record->lastValues->standard->url);

        $this->assertInstanceOf(HttpRedirection::class, $record->value);
        $this->assertFalse($record->value->hard);
        $this->assertEquals(302, $record->value->redirectType);
        $this->assertEquals('My Website', $record->value->title);
        $this->assertEquals('A website containing example data', $record->value->description);
        $this->assertEquals('Example', $record->value->keywords);
        $this->assertEquals('https://www.example.com', $record->value->url);
    }

    public function testDataSentToApiCorrectly(): void
    {
        $history = &$this->history();

        $record = $this->domain->records->create();
        $record->name = 'www';
        $record->type = RecordType::HTTP();

        $value = new HttpRedirection();
        $value->hard = false;
        $value->redirectType = 302;
        $value->title = 'My Website';
        $value->description = 'A website containing example data';
        $value->keywords = 'Example';
        $value->url = 'https://www.example.com';

        $record->setValue($value);

        $this->assertEquals(RecordMode::STANDARD(), $record->mode);

        $this->mock->append(new Response(201, [], $this->getFixture('responses/domainrecord/create.json')));
        $record->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/domainrecord/create-httpredirection.json'), $history[0]['request']->getBody());
    }
}
