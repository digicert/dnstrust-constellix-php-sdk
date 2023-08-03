<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Traits\DomainAware;

/**
 * Manages domain history snapshots
 * @package Constellix\Client\Managers
 */
class DomainSnapshotManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/snapshots';

    public function get(int $version): DomainSnapshot
    {
        return $this->getObject($version);
    }

    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    protected function getIdPropertyName(): string
    {
        return 'version';
    }

    public function apply(DomainSnapshot $snapshot): void
    {
        $url = $this->getObjectUri($snapshot) . "/apply";
        $this->client->post($url);
    }

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
