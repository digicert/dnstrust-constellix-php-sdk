<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Interfaces\Models\Basic\BasicContactListInterface;
use Constellix\Client\Interfaces\Models\ContactListInterface;
use Constellix\Client\Models\Common\CommonContactList;

/**
 * Represents a concise representation of a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property int $emailsCount
 * @property-read ContactListInterface $full;
 */
class BasicContactList extends CommonContactList implements BasicContactListInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}