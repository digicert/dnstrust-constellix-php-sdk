<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Concise\ConciseContactList;
use Constellix\Client\Models\ContactList;
use Constellix\Client\Traits\HasPagination;

/**
 * Managed Contact List Resources.
 * @package Constellix\Client\Managers
 */
class ContactListManager extends AbstractManager
{
    use HasPagination;

    /**
     * The base URI for contact lists.
     * @var string
     */
    protected string $baseUri = '/contactlists';

    /**
     * Create a new Contact List.
     * @return ContactList
     */
    public function create(): ContactList
    {
        /**
         * @var ContactList $object
         */
        $object = $this->createObject();
        return $object;
    }

    /**
     * Fetch an existing Contact List.
     * @param int $id
     * @return ContactList
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */

    public function get(int $id): ContactList
    {
        /**
         * @var ContactList $object
         */
        $object = $this->getObject($id);
        return $object;
    }
}
