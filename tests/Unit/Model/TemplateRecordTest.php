<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Record;
use Constellix\Client\Models\Template;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TemplateRecordTest extends TestCase
{
    protected Client $api;
    protected Template $template;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $this->template = $this->api->templates->get(83675283);
    }

    // Most record tests are done for Domain Records, so we'll do the basic tests here
    public function testSaveOnNewRecords(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/templaterecord/create.json')));

        $record = $this->template->records->create();
        $record->name = 'www';

        $this->assertNull($record->id);

        $record->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/templates/83675283/records', $request->getUri()->getPath());

        $this->assertEquals(732673, $record->id);
    }

    public function testSaveOnExistingRecords(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/templaterecord/a.json')));

        $record = $this->template->records->get(732673);
        $this->assertEquals('This is my DNS record', $record->notes);

        $record->notes = 'Testing';
        $this->assertTrue($record->hasChanged('notes'));

        $this->mock->append(new Response(200, [], $this->getFixture('responses/templaterecord/a.json')));
        $record->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/templates/83675283/records/732673', $request->getUri()->getPath());
    }
    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/templaterecord/a.json')));
        $record = $this->template->records->get(732673);

        $this->assertInstanceOf(Record::class, $record);
        $this->assertSame($this->template, $record->template);
    }
}
