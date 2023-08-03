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
use Constellix\Client\Models\Basic\BasicContactList;
use Constellix\Client\Models\Basic\BasicVanityNameserver;
use Constellix\Client\Models\Common\CommonContactList;
use Constellix\Client\Models\Common\CommonDomain;
use Constellix\Client\Models\Basic\BasicTemplate;
use Constellix\Client\Models\Common\CommonTemplate;
use Constellix\Client\Models\Common\CommonVanityNameserver;
use Constellix\Client\Models\Concise\ConciseContactList;
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
 * @property ?SOA $soa
 * @property bool $geoip
 * @property bool $gtd
 * @property string $nameservers
 * @property Tag[] $tags
 * @property ?CommonTemplate $template
 * @property ?CommonVanityNameserver $vanityNameserver
 * @property CommonContactList[] $contacts
 * @property-read \DateTime $createdAt
 * @property-read \DateTime $updatedAt
 */
class Domain extends CommonDomain implements EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    /**
     * @var array<mixed>
     */
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

    /**
     * @var string[]
     */
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

    protected function hasTag(Tag $tag): bool
    {
        foreach ($this->tags as $index => $domainTag) {
            if ($tag->id == $domainTag->id) {
                return true;
            }
        }
        return false;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->hasTag($tag)) {
            $tags = $this->tags;
            $tags[] = $tag;
            $this->tags = $tags;
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
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

    protected function hasContactList(CommonContactList $contactList): bool
    {
        foreach ($this->contacts as $index => $contact) {
            if ($contactList->id == $contact->id) {
                return true;
            }
        }
        return false;
    }

    public function addContactList(CommonContactList $contactList): self
    {
        if (!$this->hasContactList($contactList)) {
            $contacts = $this->contacts;
            $contacts[] = $contactList;
            $this->contacts = $contacts;
        }
        return $this;
    }

    public function removeContactList(CommonContactList $contactList): self
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

    protected function setTemplate(int|CommonTemplate|null $template): void
    {
        if ($template === null) {
            return;
        }
        if (is_integer($template)) {
            $template = new BasicTemplate($this->client->templates, $this->client, (object) [
                'id' => $template,
            ]);
        }
        if ($template instanceof CommonTemplate) {
            $this->props['template'] = $template;
            $this->changed[] = 'template';
        }
    }

    protected function setVanityNameserver(int|CommonVanityNameserver|null $nameserver): void
    {
        if ($nameserver === null) {
            return;
        }
        if (is_integer($nameserver)) {
            $nameserver = new BasicVanityNameserver($this->client->vanitynameservers, $this->client, (object) [
                'id' => $nameserver,
            ]);
        }
        if ($nameserver instanceof CommonVanityNameserver) {
            $this->props['vanityNameserver'] = $nameserver;
            $this->changed[] = 'vanityNameserver';
        }
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
