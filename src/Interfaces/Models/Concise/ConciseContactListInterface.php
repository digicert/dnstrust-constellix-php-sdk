<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Concise;

use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Models\ContactListInterface;

/**
 * Represents a Contact List resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read string $name;
 * @property-read int $emailsCount
 * @property-read ContactListInterface $full
 */
interface ConciseContactListInterface extends CommonContactListInterface
{
}