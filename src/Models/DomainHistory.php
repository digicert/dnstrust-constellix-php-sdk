<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a poing in the domain's history.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property string $version
 */
class DomainHistory extends AbstractDomainHistory implements DomainAwareInterface
{
    use DomainAware;

    protected DomainHistoryManager $manager;

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

    public function snapshot(): DomainSnapshot
    {
        return $this->manager->snapshot($this);
    }
}
