<?php

declare(strict_types=1);

namespace Constellix\Client\Managers\ContactList;

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
        return $this->getObject($id);
    }

    /**
     * Instantiate a new Slack Webhook object.
     * @param string|null $className
     * @return SlackWebhook
     */
    protected function createObject(?string $className = null): SlackWebhook
    {
        $object = parent::createObject($className);
        $object->setContactList($this->contactList);
        return $object;
    }
}
