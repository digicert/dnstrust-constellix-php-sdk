<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\ContactListManagerInterface;
use Constellix\Client\Interfaces\Managers\TagManagerInterface;
use Constellix\Client\Interfaces\Models\ContactListInterface;
use Constellix\Client\Interfaces\Models\TagInterface;
use Constellix\Client\Models\Concise\ConciseContactList;

/**
 * Managed Contact List Resources.
 * @package Constellix\Client\Managers
 */
class ContactListManager extends AbstractManager implements ContactListManagerInterface
{
    /**
     * The base URI for contact lists.
     * @var string
     */
    protected string $baseUri = '/contactlists';

    public function create(): ContactListInterface
    {
        return $this->createObject();
    }

    public function get(int $id): ContactListInterface
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