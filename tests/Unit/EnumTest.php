<?php

namespace Constellix\Client\Tests\Unit;

use Constellix\Client\Enums\Pools\PoolValuePolicy;
use Constellix\Client\Enums\Records\RecordMode;

class EnumTest extends TestCase
{
    public function testRecordMode(): void
    {
        $enum = RecordMode::STANDARD();
        $this->assertEquals('standard', $enum->value);
        $enum = RecordMode::FAILOVER();
        $this->assertEquals('failover', $enum->value);
        $enum = RecordMode::POOLS();
        $this->assertEquals('pools', $enum->value);
        $enum = RecordMode::ROUNDROBINFAILOVER();
        $this->assertEquals('roundRobinFailover', $enum->value);
    }

    public function testPoolValuePolicy(): void
    {
        $enum = PoolValuePolicy::FOLLOW_SONAR();
        $this->assertEquals('follow_sonar', $enum->value);
        $enum = PoolValuePolicy::ALWAYS_OFF();
        $this->assertEquals('always_off', $enum->value);
        $enum = PoolValuePolicy::ALWAYS_ON();
        $this->assertEquals('always_on', $enum->value);
        $enum = PoolValuePolicy::OFF_ON_FAILURE();
        $this->assertEquals('off_on_failure', $enum->value);
    }
}
