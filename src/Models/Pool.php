<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Models\Basic\BasicDomain;
use Constellix\Client\Models\Basic\BasicTemplate;
use Constellix\Client\Models\Common\CommonContactList;
use Constellix\Client\Models\Common\CommonPool;
use Constellix\Client\Models\Basic\BasicContactList;
use Constellix\Client\Models\Helpers\ITO;
use Constellix\Client\Traits\EditableModel;

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
 * @property-read BasicDomain[] $domains
 * @property-read BasicTemplate[] $templates
 * @property CommonContactList[] $contacts
 * @property ITO $ito
 * @property PoolValue[] $values
 */
class Pool extends CommonPool implements EditableModelInterface
{
    use EditableModel;

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

        $this->props['values'] = [];
        if (property_exists($data, 'values') && $data->values) {
            $this->props['values'] = array_map(function ($valueData) {
                return new PoolValue($valueData);
            }, $data->values);
        }

        $this->props['contacts'] = [];
        if (property_exists($data, 'contacts') && $data->contacts) {
            $this->props['values'] = array_map(function ($contactData) {
                return new BasicContactList($this->client->contactlists, $this->client, $contactData);
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
        $payload->contacts = array_map(function (CommonContactList $contact) {
            return $contact->id;
        }, $this->contacts);

        return $payload;
    }

    protected function hasContactList(CommonContactList $contactList): bool
    {
        foreach ($this->contacts as $list) {
            if ($list->id == $contactList->id) {
                return true;
            }
        }
        return false;
    }

    public function addContactList(CommonContactList $contactList): self
    {
        if ($this->hasContactList($contactList)) {
            return $this;
        }

        $lists = $this->contacts;
        $lists[] = $contactList;
        $this->contacts = $lists;
        return $this;
    }

    public function removeContactList(CommonContactList $contactList): self
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
