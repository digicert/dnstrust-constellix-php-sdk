<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonVanityNameserverInterface;

/**
 * Represents a Vanity NameServer resource
 * @package Constellix\Client\Interfaces
 *
 * @property string $name
 * @property bool $default
 * @property bool $public
 * @property object $nameserverGroup
 * @property string[] $nameservers;
 */
interface VanityNameserverInterface extends CommonVanityNameserverInterface
{
    public function addNameServer(string $nameserver): self;
    public function removeNameServer(string $nameserver): self;
}