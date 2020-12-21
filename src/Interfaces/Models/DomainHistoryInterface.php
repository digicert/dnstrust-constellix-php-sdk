<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Represents a version of the domain history.
 * @package Constellix\Client\Interfaces
 *
 * @property CommonDomainInterface $domain;
 */
interface DomainHistoryInterface extends AbstractDomainHistoryInterface
{
    public function snapshot(): DomainSnapshotInterface;
}