<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Template resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
abstract class CommonTemplate extends AbstractModel implements CommonTemplateInterface, ManagedModelInterface
{
    use ManagedModel;

    protected array $props = [
        'name' => null,
    ];
}