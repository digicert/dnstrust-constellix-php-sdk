<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Represents a domain record.
 * @package Constellix\Client\Interfaces
 *
 * @property CommonDomainInterface $domain;
 */
interface DomainRecordInterface extends RecordInterface
{
}