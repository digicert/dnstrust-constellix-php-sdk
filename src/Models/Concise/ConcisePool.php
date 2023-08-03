<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Models\Basic\BasicDomain;
use Constellix\Client\Models\Basic\BasicTemplate;
use Constellix\Client\Models\Common\CommonPool;
use Constellix\Client\Models\Helpers\ITO;
use Constellix\Client\Models\Pool;

/**
 * Represents a concise representation of a Pool resource.
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
 * @property-read ITO $ito
 * @property-read ConcisePoolValue[] $values
 * @property-read Pool $full
 */
class ConcisePool extends CommonPool
{
    protected function getFull(): Pool
    {
        return $this->manager->get($this->type, $this->id);
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        $this->props['values'] = [];
        if (property_exists($data, 'values') && $data->values) {
            $this->props['values'] = array_map(function ($valueData) {
                return new ConcisePoolValue($valueData);
            }, $data->values);
        }
    }
}
