<?php

namespace Constellix\Client\Managers\ContactList;

use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Managers\AbstractManager;
use Constellix\Client\Traits\ContactListAware;
use Constellix\Client\Traits\HasPagination;

abstract class AbstractContactListItemManager extends AbstractManager implements ContactListAwareInterface
{
    use ContactListAware;
    use HasPagination;

    protected string $modelClassName;

    /**
     * The base URI for any requests to the API for this resource.
     * @return string
     */
    protected function getBaseUri(): string
    {

        return str_replace(':contactlist_id', (string)$this->contactList->id, $this->baseUri);
    }

    /**
     * Get the model class for this manager
     * @return string
     */
    protected function getModelClass(): string
    {
        return $this->modelClassName;
    }
}
