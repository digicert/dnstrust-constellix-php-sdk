<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List resource.
 * @package Constellix\Client\Models
 */
abstract class CommonContactList extends AbstractModel implements CommonContactListInterface, ManagedModelInterface
{
    use ManagedModel;

    protected array $props = [
    ];
}