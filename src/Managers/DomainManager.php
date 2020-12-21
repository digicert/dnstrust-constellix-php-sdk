<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\DomainManagerInterface;
use Constellix\Client\Interfaces\Models\DomainInterface;

/**
 * Manages Domain API resources.
 * @package Constellix\Client\Managers
 */
class DomainManager extends AbstractManager implements DomainManagerInterface
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains';

    public function create(): DomainInterface
    {
        return $this->createObject();
    }

    public function get(int $id): DomainInterface
    {
        return $this->getObject($id);
    }
}