<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Models\Common\CommonVanityNameserver;
use Constellix\Client\Models\VanityNameserver;

/**
 * Represents a concise representation of a Vanity NS resource.
 * @package Constellix\Client\Models
 *
 * @property int $emailsCount
 * @property-read VanityNameserver $full;
 */
class BasicVanityNameserver extends CommonVanityNameserver
{
    protected function getFull(): VanityNameserver
    {
        return $this->manager->get($this->id);
    }
}
