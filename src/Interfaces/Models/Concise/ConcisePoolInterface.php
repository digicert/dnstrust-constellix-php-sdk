<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Concise;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Interfaces\Models\Common\CommonPoolInterface;
use Constellix\Client\Interfaces\Models\ITOInterface;
use Constellix\Client\Interfaces\Models\PoolInterface;

/**
 * Represents a Pool resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read PoolType $type
 * @property-read string $name
 * @property-read int $return
 * @property-read int $minimumFailover
 * @property-read bool $failed
 * @property-read bool $enabled
 * @property-read BasicDomainInterface[] $domains
 * @property-read BasicTemplateInterface[] $templates
 * @property-read ITOInterface $ito
 * @property-read ConcisePoolValueInterface[] $values
 * @property-read PoolInterface $full
 */
interface ConcisePoolInterface extends CommonPoolInterface
{
}