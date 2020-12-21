<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\DomainHistoryInterface;
use Constellix\Client\Interfaces\Models\DomainSnapshotInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a poing in the domain's history.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class DomainHistory extends AbstractDomainHistory implements DomainHistoryInterface, DomainAwareInterface
{
    use DomainAware;

    protected array $props = [
        'name' => null,
    ];

    protected array $editable = [
        'name',
    ];

    public function snapshot(): DomainSnapshotInterface
    {
        return $this->manager->snapshot($this);
    }
}