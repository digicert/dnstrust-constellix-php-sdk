<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Basic;

use Constellix\Client\Interfaces\Models\Common\CommonVanityNameserverInterface;
use Constellix\Client\Interfaces\Models\VanityNameserverInterface;

/**
 * Represents a basic Vanity Nameserver resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read VanityNameserverInterface $full;
 */
interface BasicVanityNameserverInterface extends CommonVanityNameserverInterface
{
}