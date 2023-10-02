<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\DomainRecord;
use Constellix\Client\Traits\DomainAware;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages domain record resources.
 * @package Constellix\Client\Managers
 */
class DomainRecordManager extends AbstractManager implements DomainAwareInterface
{
    use DomainAware;
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/records';

    /**
     * Create a new Domain Record.
     * @return DomainRecord
     */
    public function create(): DomainRecord
    {
        /**
         * @var DomainRecord $object
         */
        $object = $this->createObject();
        return $object;
    }

    /**
     * Fetch an existing Domain Record.
     * @param int $id
     * @return DomainRecord
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */

    public function get(int $id): DomainRecord
    {
        /**
         * @var DomainRecord $object
         */
        $object = $this->getObject($id);
        return $object;
    }

    /**
     * Fetch the base URI for domain records.
     * @return string
     */
    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', (string)$this->domain->id, $this->baseUri);
    }

    /**
     * Instantiate a new DomainRecord object.
     * @param string|null $className
     * @return DomainRecord
     */

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
