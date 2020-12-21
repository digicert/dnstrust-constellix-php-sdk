<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\DomainHistoryInterface;

/**
 * Manages domain history objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface DomainHistoryManagerInterface extends AbstractManagerInterface
{
    /**
     * Gets the domain history with the specified version
     * @param int $version
     * @return DomainHistoryInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $version): DomainHistoryInterface;
}