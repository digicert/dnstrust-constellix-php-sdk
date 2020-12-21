<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\DomainRecordManagerInterface;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use Constellix\Client\Interfaces\Models\DomainRecordInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Traits\DomainAware;

/**
 * Manages domain record resources.
 * @package Constellix\Client\Managers
 */
class DomainRecordManager extends AbstractManager implements DomainRecordManagerInterface, DomainAwareInterface
{
    use DomainAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains/:domain_id/records';

    public function create(): DomainRecordInterface
    {
        return $this->createObject();
    }

    public function get(int $id): DomainRecordInterface
    {
        return $this->getObject($id);
    }

    protected function getBaseUri(): string
    {
        return str_replace(':domain_id', $this->domain->id, $this->baseUri);
    }

    protected function createObject(?string $className = null): AbstractModelInterface
    {
        $object = parent::createObject($className);
        $object->setDomain($this->domain);
        return $object;
    }
}