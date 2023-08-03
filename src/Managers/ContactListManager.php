<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Concise\ConciseContactList;
use Constellix\Client\Models\ContactList;

/**
 * Managed Contact List Resources.
 * @package Constellix\Client\Managers
 */
class ContactListManager extends AbstractManager
{
    /**
     * The base URI for contact lists.
     * @var string
     */
    protected string $baseUri = '/contactlists';

    public function create(): ContactList
    {
        return $this->createObject();
    }

    public function get(int $id): ContactList
    {
        return $this->getObject($id);
    }

    /**
     * Return the name of the model class for the concise version of the resource.
     * @return string
     * @throws \ReflectionException
     */
    protected function getConciseModelClass(): string
    {
        return ConciseContactList::class;
    }
}
