<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Represents a version of the domain history/snapshot.
 * @package Constellix\Client\Interfaces
 *
 * @property CommonDomainInterface $domain;
 */
interface AbstractDomainHistoryInterface extends AbstractModelInterface
{
    public function apply(): AbstractDomainHistoryInterface;
}