<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Managers\TemplateRecordManager;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TemplateTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $template = $this->api->templates->create();
        $this->assertEquals('Template:#', (string)$template);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $template = $this->api->templates->get(83675283);
        $this->assertEquals('Template:83675283', (string)$template);
    }

    public function testSaveOnNewTemplate(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/template/create.json')));

        $template = $this->api->templates->create();
        $template->name = 'My Template';

        $this->assertNull($template->id);
        $template->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/templates', $request->getUri()->getPath());

        $this->assertEquals(83675283, $template->id);
    }

    public function testSaveOnExistingTemplate(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));

        $template = $this->api->templates->get(83675283);
        $this->assertEquals('My Template', $template->name);

        $template->name = 'New Name';
        $this->assertTrue($template->hasChanged('name'));
        $this->assertEquals('New Name', $template->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $template->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/templates/83675283', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $template = $this->api->templates->create();
        $template->name = 'My New Template';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/template/create.json')));
        $template->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/template/create-simple.json'), $history[0]['request']->getBody());

        $template2 = $this->api->templates->create();
        $template2->name = 'My New Template';
        $template2->gtd = true;
        $template2->geoip = true;

        $this->mock->append(new Response(201, [], $this->getFixture('responses/template/create.json')));
        $template2->save();

        $this->assertCount(2, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/template/create-complex.json'), $history[1]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $template = $this->api->templates->get(83675283);

        $this->assertEquals(83675283, $template->id);
        $this->assertEquals('My Template', $template->name);
        $this->assertEquals(3, $template->version);
        $this->assertTrue($template->geoip);
        $this->assertTrue($template->gtd);

        $this->assertInstanceOf(\DateTime::class, $template->createdAt);
        $this->assertEquals('2019-08-23T14:15:22+00:00', $template->createdAt->format('c'));
        $this->assertInstanceOf(\DateTime::class, $template->updatedAt);
        $this->assertEquals('2019-08-24T14:15:22+00:00', $template->updatedAt->format('c'));
    }

    public function testUnableToGetRecordsForNewTemplate(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $template = $this->api->templates->get(83675283);
        $this->assertInstanceOf(TemplateRecordManager::class, $template->records);
        $this->assertEquals($template, $template->records->template);

        $this->expectException(ConstellixException::class);
        $this->expectExceptionMessage('Template must be created before you can access records');
        $template2 = $this->api->templates->create();
        $manager = $template2->records->template;
    }
}
