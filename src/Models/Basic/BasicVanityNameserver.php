<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Interfaces\Models\Basic\BasicVanityNameserverInterface;
use Constellix\Client\Interfaces\Models\VanityNameserverInterface;
use Constellix\Client\Models\Common\CommonVanityNameserver;

/**
 * Represents a concise representation of a Vanity NS resource.
 * @package Constellix\Client\Models
 *
 * @property int $emailsCount
 * @property-read VanityNameserverInterface $full;
 */
class BasicVanityNameserver extends CommonVanityNameserver implements BasicVanityNameserverInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}