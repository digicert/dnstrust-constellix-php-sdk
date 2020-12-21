<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\DomainSnapshotManagerInterface;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use Constellix\Client\Interfaces\Models\DomainHistoryInterface;
use Constellix\Client\Interfaces\Models\DomainRecordInterface;
use Constellix\Client\Interfaces\Models\DomainSnapshotInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Traits\DomainAware;

/**
 * Manages domain history snapshots
 * @package Constellix\Client\Managers
 */
class DomainSnapshotManager extends AbstractManager implements DomainSnapshotManagerInterface, DomainAwareInterface
{
    use DomainAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/snapshots';

    public function get(int $version): DomainSnapshotInterface
    {
        return $this->getObject($version);
    }

    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', $this->domain->id, $this->baseUri);
    }

    protected function getIdPropertyName()
    {
        return 'version';
    }

    public function apply(DomainSnapshotInterface $snapshot)
    {
        $url = $this->getObjectUri($snapshot) . "/apply";
        $this->client->post($url);
    }

    protected function createObject(?string $className = null): AbstractModelInterface
    {
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }
}