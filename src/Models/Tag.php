<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Models\TagInterface;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Tag resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class Tag extends AbstractModel implements TagInterface, EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected array $props = [
        'name' => null,
    ];

    protected array $editable = [
        'name',
    ];
}