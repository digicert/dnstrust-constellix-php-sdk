<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Managers\ContactList\EmailManager;
use Constellix\Client\Managers\ContactList\SlackWebhookManager;
use Constellix\Client\Managers\ContactList\TeamsWebhookManager;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read int $emailCount
 * @property \stdClass[] $emails
 */
class ContactList extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected ContactListManager $manager;
    protected ?SlackWebhookManager $slack = null;
    protected ?TeamsWebhookManager $teams = null;
    protected ?EmailManager $emails = null;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
    ];


    /**
     * Parses the API data and assigns it to properties on this object.
     * @param \stdClass $data
     */
    protected function parseApiData(\stdClass $data): void
    {
        unset($data->emails);
        unset($data->emailCount);
        parent::parseApiData($data);
    }

    /**
     * Get the Slack Webhook Manager for this contact list.
     * @return SlackWebhookManager
     * @throws ConstellixException
     */
    protected function getSlack(): SlackWebhookManager
    {
        if (!$this->id) {
            throw new ConstellixException('Contact list must be created before you can access Slack webhooks');
        }
        if ($this->slack === null) {
            $this->slack = new SlackWebhookManager($this->client);
            $this->slack->setContactList($this);
        }
        return $this->slack;
    }

    /**
     * Get the Teams Webhook Manager for this contact list.
     * @return SlackWebhookManager
     * @throws ConstellixException
     */
    protected function getTeams(): TeamsWebhookManager
    {
        if (!$this->id) {
            throw new ConstellixException('Contact list must be created before you can access Teams webhooks');
        }
        if ($this->teams === null) {
            $this->teams = new TeamsWebhookManager($this->client);
            $this->teams->setContactList($this);
        }
        return $this->teams;
    }

    /**
     * Get the Emails Manager for this contact list.
     * @return EmailManager
     * @throws ConstellixException
     */
    protected function getEmails(): EmailManager
    {
        if (!$this->id) {
            throw new ConstellixException('Contact list must be created before you can access Teams webhooks');
        }
        if ($this->emails === null) {
            $this->emails = new EmailManager($this->client);
            $this->emails->setContactList($this);
        }
        return $this->emails;
    }
}
