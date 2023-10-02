<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\AbstractDomainHistory;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainHistory;
use Constellix\Client\Models\DomainSnapshot;
use Constellix\Client\Traits\DomainAware;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages domain history resources.
 * @package Constellix\Client\Managers
 */
class DomainHistoryManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/history';

    /**
     * Fetch a specific version of the domain's history.
     * @param int $version
     * @return DomainHistory
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $version): DomainHistory
    {
        /**
         * @var DomainHistory $object
         */
        $object = $this->getObject($version);
        return $object;
    }

    /**
     * The base URI for any requests to the API for this resource.
     * @return string
     */
    protected function getBaseUri(): string
    {

        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    /**
     * Instantiate a new DomainHistory object.
     * @param string|null $className
     * @return DomainHistory
     */
    protected function createObject(?string $className = null): DomainHistory
    {
        /**
         * @var DomainHistory $object
         */
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }

    /**
     * The unique ID property name for this resource.
     * @return string
     */
    protected function getIdPropertyName(): string
    {
        return 'version';
    }

    /**
     * Apply a version of the Domain to the Domain itself.
     * @param AbstractDomainHistory $history
     * @return void
     * @throws ConstellixException
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\JsonDecodeException
     * @internal
     */

    public function apply(AbstractDomainHistory $history): void
    {
        $url = $this->getObjectUri($history) . "/apply";
        $this->client->post($url);
    }

    /**
     * Take a snapshot of a specific version of domain history.
     * @param AbstractDomainHistory $history
     * @return DomainSnapshot
     * @throws ConstellixException
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\JsonDecodeException
     * @internal
     */

    public function snapshot(AbstractDomainHistory $history): DomainSnapshot
    {
        $url = $this->getObjectUri($history) . "/snapshot";
        $response = $this->client->post($url);
        if (!$response) {
            throw new ConstellixException('No data returned from API');
        }
        $snapshot = new DomainSnapshot($this->domain->snapshots, $this->client, $response->data);
        $snapshot->setDomain($this->domain);
        return $snapshot;
    }
}
