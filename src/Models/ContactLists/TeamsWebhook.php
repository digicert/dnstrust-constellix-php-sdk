<?php

namespace Constellix\Client\Models\ContactLists;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Managers\ContactList\TeamsWebhookManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ContactListAware;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List Teams Webhook resource
 * @package Constellix\Client\Models
 *
 * @property string $webhook
 * @property string $channel
 */
class TeamsWebhook extends AbstractModel implements ContactListAwareInterface
{
    use ContactListAware;
    use ManagedModel;
    use EditableModel {
        save as saveEx;
    }

    protected TeamsWebhookManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'webhook' => null,
        'channel' => null,
    ];


    /**
     * @var array<string>
     */
    protected array $editable = [
        'webhook',
        'channel',
    ];


    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        unset($data->contactlist);
        parent::parseApiData($data);
    }

    /**
     * Saves the Webhook object. This is only possible on new instances, existing objects can only be
     * deleted.
     * @return void
     * @throws ConstellixException
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     */
    public function save(): void
    {
        if ($this->id !== null) {
            throw new ConstellixException('Unable to update existing Contact List Teams Webhook objects');
        }
        $this->saveEx();
    }
}
