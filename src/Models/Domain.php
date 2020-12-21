<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\DomainStatus;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Managers\DomainHistoryManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainRecordManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainSnapshotManagerInterface;
use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;
use Constellix\Client\Interfaces\Models\Common\CommonVanityNameserverInterface;
use Constellix\Client\Interfaces\Models\Concise\ConciseContactListInterface;
use Constellix\Client\Interfaces\Models\DomainInterface;
use Constellix\Client\Interfaces\Models\TagInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Managers\DomainRecordManager;
use Constellix\Client\Managers\DomainSnapshotManager;
use Constellix\Client\Models\Basic\BasicContactList;
use Constellix\Client\Models\Basic\BasicVanityNameserver;
use Constellix\Client\Models\Common\CommonDomain;
use Constellix\Client\Models\Basic\BasicTemplate;
use Constellix\Client\Models\Common\CommonTemplate;
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
 * @property object $soa
 * @property bool $geoip
 * @property bool $gtd
 * @property string $nameservers
 * @property object[] $tags
 * @property object $template
 * @property object $vanityNameserver
 * @property ConciseContactListInterface[] $contacts
 * @property-read \DateTime $createdAt
 * @property-read \DateTime $updatedAt
 * @property-read DomainSnapshotManagerInterface $snapshots
 * @property-read DomainHistoryManagerInterface $history
 * @property-read DomainRecordManagerInterface $records
 */
class Domain extends CommonDomain implements DomainInterface, EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected ?DomainSnapshotManagerInterface $snapshots = null;
    protected ?DomainHistoryManagerInterface $history = null;
    protected ?DomainRecordManagerInterface  $records = null;

    protected array $props = [
        'name' => null,
        'note' => null,
        'status' => null,
        'version' => null,
        'soa' => null,
        'geoip' => null,
        'gtd' => null,
        'tags' => [],
        'template' => null,
        'vanityNameserver' => null,
        'contacts' => [],
        'createdAt' => null,
        'updatedAt' => null,
    ];

    protected array $editable = [
        'name',
        'note',
        'soa',
        'geoip',
        'gtd',
        'template',
        'vanityNameserver',
        'contacts',
        'tags',

    ];

    protected function getRecords()
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access records');
        }
        if (!$this->records) {
            $this->records = new DomainRecordManager($this->client);
            $this->records->setDomain($this);
        }
        return $this->records;
    }

    protected function getHistory()
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access history');
        }
        if (!$this->history) {
            $this->history = new DomainHistoryManager($this->client);
            $this->history->setDomain($this);
        }
        return $this->history;
    }

    protected function getSnapshots()
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access snapshots');
        }
        if (!$this->snapshots) {
            $this->snapshots = new DomainSnapshotManager($this->client);
            $this->snapshots->setDomain($this);
        }
        return $this->snapshots;
    }

    protected function setInitialProperties()
    {
        $this->props['soa'] = new SOA;
    }

    public function transformForApi(): object
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

        $payload->tags = array_map(function($tag) {
            return $tag->id;
        }, $this->tags);

        $payload->contacts = array_map(function($contact) {
            return $contact->id;
        }, $this->contacts);

        if ($this->soa) {
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

    protected function hasTag(TagInterface $tag): bool
    {
        foreach ($this->tags as $index => $domainTag) {
            if ($tag->id == $domainTag->id) {
                return true;
            }
        }
        return false;
    }

    public function addTag(TagInterface $tag): self
    {
        if (!$this->hasTag($tag)) {
            $tags = $this->tags;
            $tags[] = $tag;
            $this->tags = $tags;
        }
        return $this;
    }

    public function removeTag(TagInterface $tag): self
    {
        $tags = $this->tags;
        foreach ($tags as $index => $domainTag) {
            if ($tag->id == $domainTag->id) {
                unset($tags[$index]);
                $this->tags = $tags;
                break;
            }
        }
        return $this;
    }

    protected function hasContactList(CommonContactListInterface $contactList): bool
    {
        foreach ($this->contacts as $index => $contact) {
            if ($contactList->id == $contact->id) {
                return true;
            }
        }
        return false;
    }

    public function addContactList(CommonContactListInterface $contactList): self
    {
        if (!$this->hasContactList($contactList)) {
            $contacts = $this->contacts;
            $contacts[] = $contactList;
            $this->contacts = $contacts;
        }
        return $this;
    }

    public function removeContactList(CommonContactListInterface $contactList): self
    {
        $contacts = $this->contacts;
        foreach ($contacts as $index => $contact) {
            if ($contactList->id == $contact->id) {
                unset($contacts[$index]);
                $this->contacts = $contacts;
                break;
            }
        }
        return $this;
    }

    protected function setTemplate($template)
    {
        if (is_integer($template)) {
            $template = new BasicTemplate($this->client->templates, $this->client, (object) [
                'id' => $template,
            ]);
        }
        if ($template instanceof CommonTemplateInterface) {
            $this->props['template'] = $template;
            $this->changed[] = 'template';
        }
    }

    protected function setVanityNameserver($nameserver)
    {
        if (is_integer($nameserver)) {
            $nameserver = new BasicVanityNameserver($this->client->vanitynameservers, $this->client, (object) [
                'id' => $nameserver,
            ]);
        }
        if ($nameserver instanceof CommonVanityNameserverInterface) {
            $this->props['vanityNameserver'] = $nameserver;
            $this->changed[] = 'vanityNameserver';
        }
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'createdAt')) {
            $this->props['createdAt'] = new \DateTime($data->createdAt);
        }
        if (property_exists($data, 'updatedAt')) {
            $this->props['updatedAt'] = new \DateTime($data->updatedAt);
        }

        if (property_exists($data, 'vanityNameserver') && $data->vanityNameserver) {
            $this->props['vanityNameserver'] = new BasicVanityNameserver($this->client->vanitynameservers, $this->client, $data->vanityNameserver);
        }

        if (property_exists($data, 'template') && $data->template) {
            $this->props['template'] = new BasicTemplate($this->client->templates, $this->client, $data->template);
        }

        if (property_exists($data, 'soa') && $data->soa) {
            $this->props['soa'] = new SOA($data->soa);
        }

        if (property_exists($data, 'contacts') && $data->contacts) {
            $this->props['contacts'] = array_map(function ($data) {
                return new BasicContactList($this->client->contactlists, $this->client, $data);
            }, $data->contacts);
        }

        if (property_exists($data, 'tags') && $data->tags) {
            $this->props['tags'] = array_map(function ($data) {
                return new Tag($this->client->tags, $this->client, $data);
            }, $data->tags);
        }
    }
}