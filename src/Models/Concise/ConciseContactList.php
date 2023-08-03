<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Concise;

use Constellix\Client\Models\Basic\BasicContactList;
use Constellix\Client\Models\ContactList;

/**
 * Represents a concise representation of a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property-read string $name;
 * @property-read int $emailsCount
 * @property-read ContactList $full;
 */
class ConciseContactList extends BasicContactList
{
    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'emailCount' => null,
    ];
}
