<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Represents a snapshot of a domain's history.
 * @package Constellix\Client\Interfaces
 *
 * @property CommonDomainInterface $domain;
 */
interface DomainSnapshotInterface extends AbstractDomainHistoryInterface
{
    public function delete(): void;
}