<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Interfaces\Models\Concise\ConciseContactListInterface;
use Constellix\Client\Interfaces\Models\ContactListInterface;
use Constellix\Client\Models\Basic\BasicContactList;

/**
 * Represents a concise representation of a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property-read string $name;
 * @property-read int $emailsCount
 * @property-read ContactListInterface $full;
 */
class ConciseContactList extends BasicContactList implements ConciseContactListInterface
{
    protected array $props = [
        'name' => null,
        'emailCount' => null,
    ];
}