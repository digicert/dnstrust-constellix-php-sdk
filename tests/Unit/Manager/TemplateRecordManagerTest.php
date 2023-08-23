<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\TemplateRecordManager;
use Constellix\Client\Models\Template;
use Constellix\Client\Models\TemplateRecord;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TemplateRecordManagerTest extends TestCase
{
    protected Client $api;
    protected Template $template;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();

        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/template/get.json')));
        $this->template = $this->api->templates->get(83675283);
    }

    public function testManagerCreation(): void
    {
        $this->assertInstanceOf(TemplateRecordManager::class, $this->template->records);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/templaterecord/list.json')));
        $page = $this->template->records->paginate();
        $this->assertCount(1, $page);
        $this->assertInstanceOf(TemplateRecord::class, $page[0]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/templates/83675283/records', $request->getUri()->getPath());
    }

    public function testFetchingSingleRecord(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], (string)file_get_contents(__DIR__ . '/../fixtures/templaterecord/a.json')));
        $record = $this->template->records->get(732673);
        $this->assertInstanceOf(TemplateRecord::class, $record);
        $this->assertEquals(732673, $record->id);
        $this->assertSame($this->template, $record->template);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/v4/templates/83675283/records/732673', $request->getUri()->getPath());
    }

    public function testCreation(): void
    {
        $record = $this->template->records->create();
        $this->assertInstanceOf(TemplateRecord::class, $record);
        $this->assertSame($this->template, $record->template);
    }
}
