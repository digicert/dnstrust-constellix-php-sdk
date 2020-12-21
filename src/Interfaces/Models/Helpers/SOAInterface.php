<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Helpers;

use Constellix\Client\Interfaces\Models\AbstractModelInterface;

/**
 * Represents SOA configuration
 * @package Constellix\Client\Interfaces
 *
 * @property string $primaryNameserver
 * @property string $email
 * @property int $ttl
 * @property-read int $serial
 * @property int $refresh
 * @property int $retry
 * @property int $expire
 * @property int $negativeCache
 */
interface SOAInterface extends AbstractModelInterface
{
}