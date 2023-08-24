<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Client;
use Constellix\Client\Models\Helpers\NameserverGroup;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

class TagTest extends TestCase
{
    protected Client $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
    }

    public function testToString(): void
    {
        $tag = $this->api->tags->create();
        $this->assertEquals('Tag:#', (string)$tag);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/get.json')));
        $tag = $this->api->tags->get(824);
        $this->assertEquals('Tag:824', (string)$tag);
    }

    public function testSaveOnNewTag(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(201, [], $this->getFixture('responses/tag/create.json')));

        $tag = $this->api->tags->create();
        $tag->name = 'My Tag';

        $this->assertNull($tag->id);
        $tag->save();

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/v4/tags', $request->getUri()->getPath());

        $this->assertEquals(824, $tag->id);
    }

    public function testSaveOnExistingTag(): void
    {
        $history = &$this->history();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/get.json')));

        $tag = $this->api->tags->get(824);
        $this->assertEquals('My Tag', $tag->name);

        $tag->name = 'New Name';
        $this->assertTrue($tag->hasChanged('name'));
        $this->assertEquals('New Name', $tag->name);

        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/get.json')));
        $tag->save();

        $this->assertCount(2, $history);
        $request = $history[1]['request'];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/v4/tags/824', $request->getUri()->getPath());
    }

    public function testCorrectDataSentToApi(): void
    {
        $history = &$this->history();

        $tag = $this->api->tags->create();
        $tag->name = 'My Tag';

        $this->mock->append(new Response(201, [], $this->getFixture('responses/tag/create.json')));
        $tag->save();

        $this->assertCount(1, $history);
        $this->assertJsonStringEqualsJsonString($this->getFixture('requests/tag/create-simple.json'), $history[0]['request']->getBody());
    }

    public function testApiDataParsedCorrectly(): void
    {
        $this->mock->append(new Response(200, [], $this->getFixture('responses/tag/get.json')));
        $tag = $this->api->tags->get(824);

        $this->assertEquals(824, $tag->id);
        $this->assertEquals('My Tag', $tag->name);
    }
}
