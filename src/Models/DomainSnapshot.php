<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\DomainSnapshotInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a snapshot in the domain's history.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class DomainSnapshot extends AbstractDomainHistory implements DomainSnapshotInterface, DomainAwareInterface
{
    use DomainAware;

    protected array $props = [
        'name' => null,
    ];

    protected array $editable = [
        'name',
    ];

    public function delete(): void
    {
        $this->manager->delete($this);
    }
}