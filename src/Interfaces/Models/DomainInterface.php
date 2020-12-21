<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Enums\DomainStatus;
use Constellix\Client\Interfaces\Managers\DomainHistoryManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainRecordManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainSnapshotManagerInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicVanityNameserverInterface;
use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Represents a Domain resource
 * @package Constellix\Client\Interfaces
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
 * @property BasicTemplateInterface $template
 * @property BasicVanityNameserverInterface $vanityNameserver
 * @property CommonContactListInterface[] $contacts
 * @property-read \DateTime $createdAt
 * @property-read \DateTime $updatedAt
 * @property-read DomainSnapshotManagerInterface $snapshots
 * @property-read DomainHistoryManagerInterface $history
 * @property-read DomainRecordManagerInterface $records
 */
interface DomainInterface extends CommonDomainInterface
{
    public function addTag(TagInterface $tag): self;
    public function removeTag(TagInterface $tag): self;
    public function addContactList(CommonContactListInterface $contactList): self;
    public function removeContactList(CommonContactListInterface $contactList): self;
}