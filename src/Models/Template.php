<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\TemplateInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Template resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read int $version
 * @property bool $geoip
 * @property bool $gtd
 * @property \DateTime $createdAt
 * @property \DateTime $updatedAt
 */
class Template extends AbstractModel implements TemplateInterface, EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected array $props = [
        'name' => null,
        'version' => null,
        'geoip' => null,
        'gtd' => null,
        'createdAt' => null,
        'updatedAt' => null,
    ];

    protected array $editable = [
        'name',
        'geoip',
        'gtd',
    ];

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'createdAt')) {
            $this->props['createdAt'] = new \DateTime($data->createdAt);
        }
        if (property_exists($data, 'updatedAt')) {
            $this->props['updatedAt'] = new \DateTime($data->updatedAt);
        }
    }
}