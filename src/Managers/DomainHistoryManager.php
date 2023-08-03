<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\AbstractDomainHistory;
use Constellix\Client\Models\DomainHistory;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Traits\DomainAware;

/**
 * Manages domain history resources.
 * @package Constellix\Client\Managers
 */
class DomainHistoryManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/history';

    public function get(int $version): DomainHistory
    {
        return $this->getObject($version);
    }

    protected function getBaseUri(): string
    {

        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    protected function createObject(?string $className = null): DomainHistory
    {
        /**
         * @var DomainHistory $object
         */
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }

    protected function getIdPropertyName(): string
    {
        return 'version';
    }

    public function apply(AbstractDomainHistory $history): void
    {
        $url = $this->getObjectUri($history) . "/apply";
        $this->client->post($url);
    }

    public function snapshot(AbstractDomainHistory $history): DomainSnapshot
    {
        $url = $this->getObjectUri($history) . "/snapshot";
        $this->client->post($url);
        // The snapshot data is the same as a history object
        $data = $history->jsonSerialize();
        $snapshot = new DomainSnapshot($this->domain->snapshots, $this->client, $data);
        $snapshot->setDomain($this->domain);
        return $snapshot;
    }
}
