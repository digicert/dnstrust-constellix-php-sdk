<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\AbstractDomainHistoryInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Traits\DomainAware;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a point in the domain's history
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
abstract class AbstractDomainHistory extends AbstractModel implements DomainAwareInterface
{
    use DomainAware;
    use ManagedModel;

    protected DomainHistoryManager $manager;

    protected array $props = [
        'name' => null,
        'version' => null,
        'updatedAt' => null
    ];

    protected function parseApiData(\stdClass $data): void
    {
        $this->props['name'] = $data->name;
        $this->props['version'] = $data->version;
        $this->props['updatedAt'] = new \DateTime($data->updatedAt);
    }

    public function jsonSerialize(): \stdClass
    {
        $data = parent::jsonSerialize();
        unset($data->id);
        return $data;
    }

    public function apply(): AbstractDomainHistory
    {
        $this->manager->apply($this);
        return $this;
    }
}
