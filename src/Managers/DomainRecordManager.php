<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\DomainRecord;
use Constellix\Client\Traits\DomainAware;

/**
 * Manages domain record resources.
 * @package Constellix\Client\Managers
 */
class DomainRecordManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/records';

    public function create(): DomainRecord
    {
        return $this->createObject();
    }

    public function get(int $id): DomainRecord
    {
        return $this->getObject($id);
    }

    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    protected function createObject(?string $className = null): DomainRecord
    {
        /**
         * @var DomainRecord $object
         */
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }
}
