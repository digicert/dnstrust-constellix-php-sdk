<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Interfaces\Models\Basic\BasicDomainInterface;
use Constellix\Client\Interfaces\Models\Concise\ConcisePoolInterface;
use Constellix\Client\Interfaces\Models\Concise\ConcisePoolValueInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\PoolInterface;
use Constellix\Client\Models\Common\CommonPool;

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
 * @property-read BasicDomainInterface[] $domains
 * @property-read BasicTemplateInterface[] $templates
 * @property-read ITOInterface $ito
 * @property-read ConcisePoolValueInterface[] $values
 * @property-read PoolInterface $full
 */
class ConcisePool extends CommonPool implements ConcisePoolInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->type, $this->id);
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        $this->props['values'] = [];
        if (property_exists($data, 'values') && $data->values) {
            $this->props['values'] = array_map(function($valueData) {
                return new ConcisePoolValue($valueData);
            }, $data->values);
        }
    }
}