<?php

declare(strict_types=1);

namespace Constellix\Client\Managers\ContactList;

use Constellix\Client\Models\ContactLists\Email;
use Constellix\Client\Models\ContactLists\SlackWebhook;

/**
 * Manages contact list Slack Webhook resources
 * @package Constellix\Client\Managers
 */
class SlackWebhookManager extends AbstractContactListItemManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/contactlists/:contactlist_id/slack';
    protected string $modelClassName = SlackWebhook::class;

    /**
     * Fetch a specific Slack webhook
     * @param int $id
     * @return SlackWebhook
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): SlackWebhook
    {
        /**
         * @var SlackWebhook $object
         */
        $object = $this->getObject($id);
        return $object;
    }

    /**
     * Create a new SLack Webhook for the Contact List.
     * @return SlackWebhook
     */
    public function create(): SlackWebhook
    {
        return $this->createObject();
    }

    /**
     * Instantiate a new Slack Webhook object.
     * @param string|null $className
     * @return SlackWebhook
     */
    protected function createObject(?string $className = null): SlackWebhook
    {
        /**
         * @var SlackWebhook $object
         */
        $object = parent::createObject($className);
        $object->setContactList($this->contactList);
        return $object;
    }
}
