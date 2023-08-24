<?php

namespace Constellix\Client\Tests\Unit\Model;

use Constellix\Client\Models\Pool;
use Constellix\Client\Models\PoolValue;
use Constellix\Client\Tests\Unit\TestCase;

class PoolValueTest extends TestCase
{
    public function testRefreshDoesNothing(): void
    {
        $history = &$this->history();
        $this->assertCount(0, $history);
        $value = new PoolValue();
        $value->refresh();
        $this->assertCount(0, $history);
    }
    public function testToString(): void
    {
        $value = new PoolValue();
        $this->assertEquals('PoolValue', (string) $value);

        $value->value = '127.0.0.1';
        $this->assertEquals('127.0.0.1', $value);
    }
}
