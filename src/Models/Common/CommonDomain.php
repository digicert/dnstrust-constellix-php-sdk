<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Domain resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
abstract class CommonDomain extends AbstractModel implements CommonDomainInterface, ManagedModelInterface
{
    use ManagedModel;

    protected array $props = [
        'name' => null,
    ];
}