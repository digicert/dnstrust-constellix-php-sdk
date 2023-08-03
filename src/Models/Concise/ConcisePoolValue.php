<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Models\Basic\BasicDomain;
use Constellix\Client\Models\Basic\BasicTemplate;
use Constellix\Client\Models\Common\CommonPoolValue;
use Constellix\Client\Models\Helpers\ITO;
use Constellix\Client\Models\Pool;

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
 * @property-read BasicDomain[] $domains
 * @property-read BasicTemplate[] $templates
 * @property-read ConciseContactList[] $contacts
 * @property-read ITO $ito
 * @property-read ConcisePoolValue[] $values
 * @property-read Pool $full
 */
class ConcisePoolValue extends CommonPoolValue
{
}
