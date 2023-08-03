<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Template resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
abstract class CommonTemplate extends AbstractModel
{
    use ManagedModel;

    protected TemplateManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
    ];
}
