<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Continent;
use Constellix\Client\Interfaces\Models\IPFilterInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents an IP Filter resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property int $rulesLimit
 * @property Continent[] $continents
 * @property string[] $countries
 * @property int[] $asn;
 * @property string[] $ipv4;
 * @property string[] $ipv6;
 */
class IPFilter extends AbstractModel implements IPFilterInterface, EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected array $props = [
        'name' => null,
        'rulesLimit' => 100,
        'continents' => [],
        'countries' => [],
        'asn' => [],
        'ipv4' => [],
        'ipv6' => [],
    ];

    protected array $editable = [
        'name',
        'rulesLimit',
        'continents',
        'countries',
        'asn',
        'ipv4',
        'ipv6',
    ];

    public function transformForApi(): object
    {
        $payload = parent::transformForApi();
        $payload->continents = array_map(function($continent) {
            return $continent->value;
        }, $this->continents);
        return $payload;
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'continents')) {
            $this->props['continents'] = array_map(
                function ($continent) {
                    return Continent::make($continent);
                },
                $data->continents
            );
        }
    }

    protected function addValue(string $property, $value): self
    {
        if (!in_array($value, $this->{$property})) {
            $list = $this->{$property};
            $list[] = $value;
            $this->{$property} = $list;
        }
        return $this;
    }

    public function removeValue(string $property, $value): self
    {
        $index = array_search($value, $this->{$property});
        if ($index !== false) {
            $list = $this->{$property};
            unset($list[$index]);
            $this->{$property} = $list;
        }
        return $this;
    }

    public function addContinent(Continent $continent): IPFilterInterface
    {
        return $this->addValue('continents', $continent);
    }

    public function removeContinent(Continent $continent): IPFilterInterface
    {
        return $this->removeValue('continents', $continent);
    }

    public function addCountry(string $country): IPFilterInterface
    {
        return $this->addValue('countries', $country);
    }

    public function removeCountry(string $country): IPFilterInterface
    {
        return $this->removeValue('countries', $country);
    }

    public function addASN(int $asn): IPFilterInterface
    {
        return $this->addValue('asn', $asn);
    }

    public function removeASN(int $asn): IPFilterInterface
    {
        return $this->removeValue('asn', $asn);
    }

    public function addIPv4(string $ip): IPFilterInterface
    {
        return $this->addValue('ipv4', $ip);
    }

    public function removeIPv4(string $ip): IPFilterInterface
    {
        return $this->removeValue('ipv4', $ip);
    }

    public function addIPv6(string $ip): IPFilterInterface
    {
        return $this->addValue('ipv6', $ip);
    }

    public function removeIPv6(string $ip): IPFilterInterface
    {
        return $this->removeValue('ipv6', $ip);
    }
}