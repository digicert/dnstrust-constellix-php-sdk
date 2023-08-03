<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Models\Common\CommonContactList;
use Constellix\Client\Models\ContactList;

/**
 * Represents a concise representation of a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property int $emailsCount
 * @property-read ContactList $full;
 */
class BasicContactList extends CommonContactList
{
    protected function getFull(): ContactList
    {
        return $this->manager->get($this->id);
    }
}
