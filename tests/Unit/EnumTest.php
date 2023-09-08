<?php

namespace Constellix\Client\Tests\Unit;

use Constellix\Client\Enums\Pools\ITOHandicapFactor;
use Constellix\Client\Enums\Pools\ITORegion;
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

    public function testITORegion(): void
    {
        $enum = ITORegion::WORLD();
        $this->assertEquals('world', $enum->value);
        $enum = ITORegion::EUROPE();
        $this->assertEquals('europe', $enum->value);
        $enum = ITORegion::NAEAST();
        $this->assertEquals('naeast', $enum->value);
        $enum = ITORegion::NACENTRAL();
        $this->assertEquals('nacentral', $enum->value);
        $enum = ITORegion::NAWEST();
        $this->assertEquals('nawest', $enum->value);
        $enum = ITORegion::SOUTHAMERICA();
        $this->assertEquals('southamerica', $enum->value);
        $enum = ITORegion::ASIAPAC();
        $this->assertEquals('asiapac', $enum->value);
        $enum = ITORegion::OCEANIA();
        $this->assertEquals('oceania', $enum->value);
    }

    public function testITOHandicapFactor(): void
    {
        $enum = ITOHandicapFactor::NONE();
        $this->assertEquals('none', $enum->value);
        $enum = ITOHandicapFactor::SPEED();
        $this->assertEquals('speed', $enum->value);
        $enum = ITOHandicapFactor::PERCENT();
        $this->assertEquals('percent', $enum->value);
    }
}
