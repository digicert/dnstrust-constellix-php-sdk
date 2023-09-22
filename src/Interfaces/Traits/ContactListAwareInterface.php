<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Models\ContactList;

/**
 * Trait for objects that know about contact lists
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read ContactList $contactList
 */
interface ContactListAwareInterface
{
    /**
     * Set the contact list that relates to this object.
     * @param ContactList $contactList
     * @return ContactListAwareInterface
     */
    public function setContactList(ContactList $contactList): ContactListAwareInterface;
}
