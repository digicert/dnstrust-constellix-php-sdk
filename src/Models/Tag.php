<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Managers\TagManager;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Tag resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class Tag extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected TagManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
    ];
}
