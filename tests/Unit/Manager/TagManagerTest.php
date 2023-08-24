<?php

namespace Constellix\Client\Tests\Unit\Manager;

use Constellix\Client\Client;
use Constellix\Client\Managers\TagManager;
use Constellix\Client\Models\Tag;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TagManagerTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testManagerCreation(): void
    {
        $manager = $this->api->tags;
        $this->assertInstanceOf(TagManager::class, $manager);
    }


    public function testFetchingTags(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/get.json')));
        $tag = $this->api->tags->get(824);
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals(824, $tag->id);
        $this->assertEquals('My Tag', $tag->name);
        $this->assertTrue($tag->fullyLoaded);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/tags/824', $history[0]['request']->getUri()->getPath());
    }

    public function testCreatingTag(): void
    {
        $tag = $this->api->tags->create();
        $this->assertInstanceOf(Tag::class, $tag);
    }

    public function testFetchingList(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/list.json')));
        $page = $this->api->tags->paginate();

        $this->assertCount(1, $page);
        $this->assertInstanceOf(Tag::class, $page[0]);

        $this->assertEquals('GET', $history[0]['request']->getMethod());
        $this->assertEquals('/v4/tags', $history[0]['request']->getUri()->getPath());
    }
}
