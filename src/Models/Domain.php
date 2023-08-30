<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\DomainStatus;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Managers\DomainRecordManager;
use Constellix\Client\Managers\DomainSnapshotManager;
use Constellix\Client\Models\Helpers\SOA;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Domain resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property string $note
 * @property-read DomainStatus $status
 * @property-read int $version
 * @property bool $enabled
 * @property ?SOA $soa
 * @property bool $geoip
 * @property bool $gtd
 * @property array<string> $nameservers
 * @property Tag[] $tags
 * @property ?Template $template
 * @property ?VanityNameserver $vanityNameserver
 * @property ContactList[] $contacts
 * @property-read \DateTime $createdAt
 * @property-read \DateTime $updatedAt
 * @property-read DomainSnapshotManager $snapshots
 * @property-read DomainHistoryManager $history
 * @property-read DomainRecordManager $records
 */
class Domain extends AbstractModel implements EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'note' => null,
        'enabled' => null,
        'status' => null,
        'version' => null,
        'soa' => null,
        'geoip' => null,
        'gtd' => null,
        'tags' => [],
        'nameservers' => [],
        'template' => null,
        'vanityNameserver' => null,
        'contacts' => [],
        'createdAt' => null,
        'updatedAt' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'enabled',
        'note',
        'soa',
        'geoip',
        'gtd',
        'template',
        'vanityNameserver',
        'contacts',
        'tags',

    ];

    protected DomainManager $manager;
    protected ?DomainSnapshotManager $snapshots = null;
    protected ?DomainHistoryManager $history = null;
    protected ?DomainRecordManager $records = null;

    protected function setInitialProperties(): void
    {
        $this->props['soa'] = new SOA();
    }

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();

        $payload->vanityNameserver = null;
        if ($this->vanityNameserver) {
            $payload->vanityNameserver = $this->vanityNameserver->id;
        }

        $payload->template =  null;
        if ($this->template) {
            $payload->template = $this->template->id;
        }

        $payload->tags = array_map(function ($tag) {
            /**
             * @var \stdClass $tag
             */
            return $tag->id;
        }, $this->tags);

        $payload->contacts = array_map(function ($contact) {
            /**
             * @var \stdClass $contact
             */
            return $contact->id;
        }, $this->contacts);

        if ($this->soa !== null) {
            $payload->soa = $this->soa->transformForApi();
        }
        unset(
            $payload->createdAt,
            $payload->updatedAt,
            $payload->version,
            $payload->status,
        );
        return $payload;
    }

    public function addTag(Tag $tag): self
    {
        $this->addToCollection('tags', $tag);
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->removeFromCollection('tags', $tag);
        return $this;
    }

    public function addContactList(ContactList $contactList): self
    {
        $this->addToCollection('contacts', $contactList);
        return $this;
    }

    public function removeContactList(ContactList $contactList): self
    {
        $this->removeFromCollection('contacts', $contactList);
        return $this;
    }

    public function setTemplate(null|int|\stdClass|Template $template): void
    {
        $this->setObjectReference($this->client->templates, Template::class, 'template', $template);
    }

    public function setVanityNameserver(null|int|\stdClass|VanityNameserver $nameserver): void
    {
        $this->setObjectReference($this->client->vanitynameservers, VanityNameserver::class, 'vanityNameserver', $nameserver);
    }

    protected function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'createdAt')) {
            $this->props['createdAt'] = new \DateTime($data->createdAt);
        }
        if (property_exists($data, 'updatedAt')) {
            $this->props['updatedAt'] = new \DateTime($data->updatedAt);
        }

        if (property_exists($data, 'vanityNameserver') && $data->vanityNameserver) {
            $this->props['vanityNameserver'] = new VanityNameserver($this->client->vanitynameservers, $this->client, $data->vanityNameserver);
        }

        if (property_exists($data, 'template') && $data->template) {
            $this->props['template'] = new Template($this->client->templates, $this->client, $data->template);
        }

        if (property_exists($data, 'soa') && $data->soa) {
            $this->props['soa'] = new SOA($data->soa);
        }

        if (property_exists($data, 'contacts') && $data->contacts) {
            $this->props['contacts'] = array_map(function ($data) {
                return new ContactList($this->client->contactlists, $this->client, $data);
            }, $data->contacts);
        }

        if (property_exists($data, 'tags') && $data->tags) {
            $this->props['tags'] = array_map(function ($data) {
                return new Tag($this->client->tags, $this->client, $data);
            }, $data->tags);
        }
    }

    protected function getHistory(): DomainHistoryManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access history');
        }
        if ($this->history === null) {
            $this->history = new DomainHistoryManager($this->client);
            $this->history->setDomain($this);
        }
        return $this->history;
    }

    protected function getSnapshots(): DomainSnapshotManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access snapshots');
        }
        if ($this->snapshots === null) {
            $this->snapshots = new DomainSnapshotManager($this->client);
            $this->snapshots->setDomain($this);
        }
        return $this->snapshots;
    }

    protected function getRecords(): DomainRecordManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access records');
        }
        if ($this->records === null) {
            $this->records = new DomainRecordManager($this->client);
            $this->records->setDomain($this);
        }
        return $this->records;
    }
}
