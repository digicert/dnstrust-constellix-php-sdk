<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Carbon\Carbon;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Managers\AbstractManager;
use Constellix\Client\Traits\DomainAware;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a point in the domain's history
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read string $version
 * @property-read Carbon $updatedAt
 * @property AbstractManager $manager
 */
abstract class AbstractDomainHistory extends AbstractModel implements DomainAwareInterface
{
    use DomainAware;
    use ManagedModel;

    protected array $props = [
        'name' => null,
        'version' => null,
        'updatedAt' => null
    ];

    /**
     * Returns the string representation for this object.
     * @return string
     */
    public function __toString()
    {
        $rClass = new \ReflectionClass($this);
        $modelName = $rClass->getShortName();
        return "{$modelName}:{$this->version}";
    }

    /**
     * Parse the API data and laod it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        $this->id = $data->version;
        $this->props['version'] = $data->version;
        $this->loadedProps[] = 'version';
        if (property_exists($data, 'name')) {
            $this->props['name'] = $data->name;
            $this->loadedProps[] = 'name';
        }
        if (property_exists($data, 'updatedAt')) {
            $this->loadedProps[] = 'updatedAt';
            $this->props['updatedAt'] = new Carbon($data->updatedAt);
        }
    }

    /**
     * Serialize this object as JSON.
     * @return \stdClass
     * @internal
     */
    public function jsonSerialize(): \stdClass
    {
        $data = parent::jsonSerialize();
        unset($data->id);
        return $data;
    }

    /**
     * Apply the domain history or snapshot to the domain.
     * @return $this
     */

    public function apply(): AbstractDomainHistory
    {
        $this->manager->apply($this);
        return $this;
    }
}
