<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Traits\DomainAware;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages domain history snapshots.
 * @package Constellix\Client\Managers
 */
class DomainSnapshotManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/snapshots';

    /**
     * Fetch a specific version snapshot for a Domain.
     * @param int $version
     * @return DomainSnapshot
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $version): DomainSnapshot
    {
        return $this->getObject($version);
    }

    /**
     * Fetch the base URI on the API for this snapshot.
     * @return string
     */
    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    /**
     * Return the unique ID property name for Domain Snapshots.
     * @return string
     */

    protected function getIdPropertyName(): string
    {
        return 'version';
    }

    /**
     * Apply the specified Domain Snapshot to the Domain.
     * @param DomainSnapshot $snapshot
     * @return void
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\JsonDecodeException
     * @throws \Constellix\Client\Exceptions\ConstellixException
     * @internal
     */
    public function apply(DomainSnapshot $snapshot): void
    {
        $url = $this->getObjectUri($snapshot) . "/apply";
        $this->client->post($url);
    }

    /**
     * Instantiate a new Domain Snapshot object.
     * @param string|null $className
     * @return DomainSnapshot
     */
    protected function createObject(?string $className = null): DomainSnapshot
    {
        /**
         * @var DomainSnapshot $object
         */
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }
}
