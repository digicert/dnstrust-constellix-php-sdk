<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Interfaces\Models\Common\CommonVanityNameserverInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Vanity NS resource.
 * @package Constellix\Client\Models
 */
abstract class CommonVanityNameserver extends AbstractModel implements CommonVanityNameserverInterface, ManagedModelInterface
{
    use ManagedModel;

    protected array $props = [];
}