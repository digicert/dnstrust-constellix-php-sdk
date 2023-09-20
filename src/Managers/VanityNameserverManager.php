<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\VanityNameserver;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages Vanity NameServer API resources.
 * @package Constellix\Client\Managers
 */
class VanityNameserverManager extends AbstractManager
{
    use HasPagination;

    /**
     * The base URI for the object.
     * @var string
     */
    protected string $baseUri = '/vanitynameservers';

    /**
     * Create a new Vanity Nameserver.
     * @return VanityNameserver
     */
    public function create(): VanityNameserver
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing Vanity Nameserver.
     * @param int $id
     * @return VanityNameserver
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): VanityNameserver
    {
        return $this->getObject($id);
    }
}
