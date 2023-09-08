<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Managers\DomainSnapshotManager;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a snapshot in the domain's history.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property int $version
 */
class DomainSnapshot extends AbstractDomainHistory implements DomainAwareInterface
{
    use DomainAware;

    protected DomainSnapshotManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'version' => null,
    ];

    /**
     * Delete the domain snapshot
     * @return void
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     */
    public function delete(): void
    {
        $this->manager->delete($this);
    }
}
