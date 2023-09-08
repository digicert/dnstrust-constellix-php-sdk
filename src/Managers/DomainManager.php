<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Domain;

/**
 * Manages Domain API resources.
 * @package Constellix\Client\Managers
 */
class DomainManager extends AbstractManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains';

    /**
     * Create a new Domain.
     * @return Domain
     */
    public function create(): Domain
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing Domain.
     * @param int $id
     * @return Domain
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): Domain
    {
        return $this->getObject($id);
    }
}
