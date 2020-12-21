<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\ContactListInterface;

/**
 * Manages Contact List objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface ContactListManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Contact List.
     * @return ContactListInterface
     */
    public function create(): ContactListInterface;

    /**
     * Gets the Contact List with the specified ID.
     * @param int $id
     * @return ContactListInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): ContactListInterface;
}