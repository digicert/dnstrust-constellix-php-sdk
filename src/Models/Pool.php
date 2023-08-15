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

    protected function setInitialProperties(): void
    {
        $this->props['ito'] = new ITO();
    }

    protected function setType(string $type): void
    {
        if ($this->id) {
            throw new ReadOnlyPropertyException('Unable to set type after a Pool has been created');
        }
        $this->props['type'] = $type;
        $this->changed[] = 'type';
    }

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
            $this->props['values'] = array_map(function ($contactData) {
                return new ContactList($this->client->contactlists, $this->client, $contactData);
            }, $data->contacts);
        }
    }

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset(
            $payload->domains,
            $payload->templates,
            $payload->failed
        );
        $payload->values = array_map(function ($value) {
            return $value->transformForApi();
        }, $this->values);
        $payload->contacts = array_map(function (ContactList $contact) {
            return $contact->id;
        }, $this->contacts);

        return $payload;
    }

    protected function hasContactList(ContactList $contactList): bool
    {
        foreach ($this->contacts as $list) {
            if ($list->id == $contactList->id) {
                return true;
            }
        }
        return false;
    }

    public function addContactList(ContactList $contactList): self
    {
        if ($this->hasContactList($contactList)) {
            return $this;
        }

        $lists = $this->contacts;
        $lists[] = $contactList;
        $this->contacts = $lists;
        return $this;
    }

    public function removeContactList(ContactList $contactList): self
    {
        if (!$this->hasContactList($contactList)) {
            return $this;
        }

        $lists = $this->contacts;
        foreach ($lists as $index => $list) {
            if ($list->id == $contactList->id) {
                unset($lists[$index]);
                break;
            }
        }
        $this->contacts = $lists;
        return $this;
    }

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
