<?php

namespace Constellix\Client\Models\ContactLists;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\ContactListAwareInterface;
use Constellix\Client\Managers\ContactList\EmailManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ContactListAware;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List Email resource
 * @package Constellix\Client\Models
 *
 * @property string $address
 * @property bool $verified
 */
class Email extends AbstractModel implements ContactListAwareInterface
{
    use ContactListAware;
    use ManagedModel;
    use EditableModel {
        save as saveEx;
    }

    protected EmailManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'address' => null,
        'verified' => false,
    ];


    /**
     * @var array<string>
     */
    protected array $editable = [
        'address',
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
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     */
    public function transformForApi(): \stdClass
    {
        return (object)[
            'address' => $this->address,
        ];
    }

    /**
     * Saves the Email object. This is only possible on new instances, existing objects can only be
     * deleted.
     * @return void
     * @throws ConstellixException
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     */
    public function save(): void
    {
        if ($this->id !== null) {
            throw new ConstellixException('Unable to update existing Contact List email objects');
        }
        $this->saveEx();
    }
}
