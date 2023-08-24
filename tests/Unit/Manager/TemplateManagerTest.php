<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Models\Template;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TemplateManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->templates;
        $this->assertInstanceOf(TemplateManager::class, $manager);
    }


    public function testFetchingTemplates(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/get.json')));
        $template = $this->api->templates->get(83675283);
        $this->assertInstanceOf(Template::class, $template);
        $this->assertEquals(83675283, $template->id);
        $this->assertEquals('My Template', $template->name);
        $this->assertTrue($template->fullyLoaded);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/templates/83675283', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingTemplate(): void
    {
        $tag = $this->api->templates->create();
        $this->assertInstanceOf(Template::class, $tag);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/template/list.json')));
        $page = $this->api->templates->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(Template::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/templates', $history[0]['request']->getUri()->getPath());
    }
}
