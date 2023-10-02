<?php

declare(strict_types=1);

namespace Constellix\Client\Managers\ContactList;

use Constellix\Client\Models\ContactLists\Email;

/**
 * Manages contact list email resources
 * @package Constellix\Client\Managers
 */
class EmailManager extends AbstractContactListItemManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/contactlists/:contactlist_id/emails';
    protected string $modelClassName = Email::class;

    /**
     * Fetch a specific email
     * @param int $id
     * @return Email
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): Email
    {
        /**
         * @var Email $object
         */
        $object = $this->getObject($id);
        return $object;
    }

    /**
     * Create a new Email for the Contact List
     * @return Email
     */
    public function create(): Email
    {
        return $this->createObject(Email::class);
    }

    /**
     * Instantiate a new email object.
     * @param string|null $className
     * @return Email
     */
    protected function createObject(?string $className = null): Email
    {
        /**
         * @var Email $object
         */
        $object = parent::createObject($className);
        $object->setContactList($this->contactList);
        return $object;
    }
}
