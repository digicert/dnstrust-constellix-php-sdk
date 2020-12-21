<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Enums\Continent;

/**
 * Represents an IP Filter resource
 * @package Constellix\Client\Interfaces
 *
 * @property string $name
 * @property int $rulesLimit
 * @property Continent[] $continents
 * @property string[] $countries
 * @property int[] $asn;
 * @property string[] $ipv4;
 * @property string[] $ipv6;
 */
interface IPFilterInterface extends AbstractModelInterface
{
    public function addContinent(Continent $continent): self;
    public function removeContinent(Continent $continent): self;

    public function addCountry(string $country): self;
    public function removeCountry(string $country): self;

    public function addASN(int $asn): self;
    public function removeASN(int $asn): self;

    public function addIPv4(string $ip): self;
    public function removeIPv4(string $ip): self;

    public function addIPv6(string $ip): self;
    public function removeIPv6(string $ip): self;
}