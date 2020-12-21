<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\DomainInterface;

/**
 * Manages Domain objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface DomainManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Domain.
     * @return DomainInterface
     */
    public function create(): DomainInterface;

    /**
     * Gets the Domain with the specified ID.
     * @param int $id
     * @return DomainInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): DomainInterface;
}