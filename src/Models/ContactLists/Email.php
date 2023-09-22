<?php
namespace Constellix\Client\Models\ContactLists;

use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Managers\ContactList\EmailManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ContactListAware;
use Constellix\Client\Traits\ManagedModel;

class Email extends AbstractModel implements ContactListAwareInterface
{
    use ContactListAware;
    use ManagedModel;

    protected EmailManager $manager;

    /**
     * @var array<string>
     */
    protected array $props = [
        'address',
        'verified',
    ];


    /**
     * @var array<string>
     */
    protected array $editable = [
        'address',
    ];

    protected function parseApiData(\stdClass $data): void
    {
        unset($data->contactlist);
        parent::parseApiData($data);
    }
}