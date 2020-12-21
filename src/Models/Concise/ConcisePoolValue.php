<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Interfaces\Models\Concise\ConciseContactListInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicDomainInterface;
use Constellix\Client\Interfaces\Models\Concise\ConcisePoolValueInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\PoolInterface;
use Constellix\Client\Models\Common\CommonPoolValue;

/**
 * Represents a concise representation of a Pool Value resource.
 * @package Constellix\Client\Models
 *
 * @property-read PoolType $type
 * @property-read string $name
 * @property-read int $return
 * @property-read int $minimumFailover
 * @property-read bool $failed
 * @property-read bool $enabled
 * @property-read BasicDomainInterface[] $domains
 * @property-read BasicTemplateInterface[] $templates
 * @property-read ConciseContactListInterface[] $contacts
 * @property-read ITOInterface $ito
 * @property-read ConcisePoolValueInterface[] $values
 * @property-read PoolInterface $full
 */
class ConcisePoolValue extends CommonPoolValue implements ConcisePoolValueInterface
{
}