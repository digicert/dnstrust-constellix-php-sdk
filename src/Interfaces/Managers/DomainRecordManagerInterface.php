<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\DomainRecordInterface;

/**
 * Manages domain records objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface DomainRecordManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new domain record.
     * @return DomainRecordInterface
     */
    public function create(): DomainRecordInterface;

    /**
     * Gets the domain record with the specified ID.
     * @param int $id
     * @return DomainRecordInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): DomainRecordInterface;
}