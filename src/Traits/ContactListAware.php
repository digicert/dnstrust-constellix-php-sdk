<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Models\ContactList;

trait ContactListAware
{
    /**
     * @var ContactList The Contact List for this object
     */
    public ContactList $contactList;

    /**
     * Set the ContactList for this object.
     * @param ContactList $contactList
     * @return ContactListAwareInterface
     * @internal
     */
    public function setContactList(ContactList $contactList): ContactListAwareInterface
    {
        $this->contactList = $contactList;
        return $this;
    }
}
