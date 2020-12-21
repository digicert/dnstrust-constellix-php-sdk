<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Models\Common\CommonPoolInterface;
use Constellix\Client\Interfaces\Models\Concise\ConciseContactListInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicDomainInterface;
use Constellix\Client\Interfaces\Models\Basic\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;

/**
 * Represents a Pool resource
 * @package Constellix\Client\Interfaces
 *
 * @property PoolType $type
 * @property string $name
 * @property int $return
 * @property int $minimumFailover
 * @property-read bool $failed
 * @property bool $enabled
 * @property-read BasicDomainInterface[] $domains
 * @property-read BasicTemplateInterface[] $templates
 * @property ConciseContactListInterface[] $contacts
 * @property ITOInterface $ito
 * @property PoolValueInterface[] $values
 */
interface PoolInterface extends CommonPoolInterface
{
    /**
     * Add a contact list to be notified when this pool changes
     * @param CommonContactListInterface $contactList
     * @return $this
     */

    public function addContactList(CommonContactListInterface $contactList): self;
    /**
     * Remove a contact list from being notified when this pool changes
     * @param CommonContactListInterface $contactList
     * @return $this
     */
    public function removeContactList(CommonContactListInterface $contactList): self;

    /**
     * Add a new value to this pool. This is not saved until you save the pool.
     * @param ?string $value;
     * @return PoolValueInterface
     */
    public function createValue(?string $value = null): PoolValueInterface;
}