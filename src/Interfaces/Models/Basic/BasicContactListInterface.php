<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Basic;

use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;
use Constellix\Client\Interfaces\Models\ContactListInterface;

/**
 * Represents a Contact List resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read ContactListInterface $full
 */
interface BasicContactListInterface extends CommonContactListInterface
{
}