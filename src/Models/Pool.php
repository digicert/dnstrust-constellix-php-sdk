<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Managers\PoolManager;
use Constellix\Client\Models\Helpers\ITO;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Pool resource.
 * @package Constellix\Client\Models
 *
 * @property PoolType $type
 * @property string $name
 * @property int $return
 * @property int $minimumFailover
 * @property-read bool $failed
 * @property bool $enabled
 * @property-read Domain[] $domains
 * @property-read Template[] $templates
 * @property ContactList[] $contacts
 * @property ITO $ito
 * @property PoolValue[] $values
 */
class Pool extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected PoolManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'type' => null,
        'return' => null,
        'minimumFailover' => null,
        'failed' => null,
        'enabled' => null,
        'values' => [],
        'domains' => [],
        'templates' => [],
        'contacts' => [],
        'ito' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'type',
        'return',
        'minimumFailover',
        'enabled',
        'values',
        'contacts',
        'ito',
    ];

    /**
     * Set initial properties on this Pool.
     * @return void
     */
    protected function setInitialProperties(): void
    {
        $this->props['ito'] = new ITO();
    }

    /**
     * Set the type of Pool. This can only be done on Pool creation.
     * @param string|PoolType $type
     * @return void
     * @throws ReadOnlyPropertyException
     */
    public function setType(string|PoolType $type): void
    {
        if ($this->id) {
            throw new ReadOnlyPropertyException('Unable to set type after a Pool has been created');
        }
        if (is_string($type)) {
            $type = PoolType::from($type);
        }
        $this->props['type'] = $type;
        $this->changed[] = 'type';
    }

    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'type') && $data->type) {
            $this->props['type'] = PoolType::make($data->type);
        }

        $this->props['domains'] = [];
        if (property_exists($data, 'domains') && $data->domains) {
            $this->props['domains'] = array_map(function ($domainData) {
                return new Domain($this->client->domains, $this->client, $domainData);
            }, $data->domains);
        }

        $this->props['templates'] = [];
        if (property_exists($data, 'templates') && $data->templates) {
            $this->props['templates'] = array_map(function ($templateData) {
                return new Template($this->client->templates, $this->client, $templateData);
            }, $data->templates);
        }

        if (property_exists($data, 'ito') && $data->ito) {
            $this->props['ito'] = new ITO($data->ito);
        }

        $this->props['values'] = [];
        if (property_exists($data, 'values') && $data->values) {
            $this->props['values'] = array_map(function ($valueData) {
                return new PoolValue($valueData);
            }, $data->values);
        }

        $this->props['contacts'] = [];
        if (property_exists($data, 'contacts') && $data->contacts) {
            $this->props['contacts'] = array_map(function ($contactData) {
                return new ContactList($this->client->contactlists, $this->client, $contactData);
            }, $data->contacts);
        }
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset(
            $payload->domains,
            $payload->templates,
            $payload->failed
        );
        $payload->ito = $this->ito->transformForApi();
        $payload->values = array_map(function ($value) {
            return $value->transformForApi();
        }, $this->values);
        $payload->contacts = array_map(function (ContactList $contact) {
            return $contact->id;
        }, $this->contacts);

        return $payload;
    }

    /**
     * Add a Contact List to this pool.
     * @param ContactList $contactList
     * @return $this
     */
    public function addContactList(ContactList $contactList): self
    {
        $this->addToCollection('contacts', $contactList);
        return $this;
    }

    /**
     * Remove a Contact List from this pool.
     * @param ContactList $contactList
     * @return $this
     */
    public function removeContactList(ContactList $contactList): self
    {
        $this->removeFromCollection('contacts', $contactList);
        return $this;
    }

    /**
     * Create a new Pool Value and add it to this Pool.
     * @param string|null $value
     * @return PoolValue
     */
    public function createValue(?string $value = null): PoolValue
    {
        $data = (object) [];
        if ($value) {
            $data->value = $value;
        }
        $poolValue = new PoolValue($data);
        $values = $this->values;
        $values[] = $poolValue;
        $this->values = $values;
        return $poolValue;
    }
}
