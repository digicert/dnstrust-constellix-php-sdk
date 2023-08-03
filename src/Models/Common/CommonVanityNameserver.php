<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Managers\VanityNameserverManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Vanity NS resource.
 * @package Constellix\Client\Models
 */
abstract class CommonVanityNameserver extends AbstractModel
{
    use ManagedModel;

    protected VanityNameserverManager $manager;
}
