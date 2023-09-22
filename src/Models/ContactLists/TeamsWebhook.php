<?php
namespace Constellix\Client\Models\ContactLists;

use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Managers\ContactList\TeamsWebhookManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ContactListAware;
use Constellix\Client\Traits\ManagedModel;

class TeamsWebhook extends AbstractModel implements ContactListAwareInterface
{
    use ContactListAware;
    use ManagedModel;

    protected TeamsWebhookManager $manager;

    /**
     * @var array<string>
     */
    protected array $props = [
        'webhook',
        'channel',
    ];


    /**
     * @var array<string>
     */
    protected array $editable = [
        'webhook',
        'channel',
    ];

    protected function parseApiData(\stdClass $data): void
    {
        unset($data->contactlist);
        parent::parseApiData($data);
    }
}