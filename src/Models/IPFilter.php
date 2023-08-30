<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Continent;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Models\Helpers\IPFilterRegion;
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
 * @property IPFilterRegion[] $regions
 */
class IPFilter extends AbstractModel implements EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected IPFilterManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'rulesLimit' => 100,
        'continents' => [],
        'countries' => [],
        'asn' => [],
        'ipv4' => [],
        'ipv6' => [],
        'regions' => [],
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'rulesLimit',
        'continents',
        'countries',
        'asn',
        'ipv4',
        'ipv6',
        'regions',
    ];

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->continents = array_map(function ($continent) {
            return $continent->value;
        }, $this->continents);
        $payload->regions = array_map(function (IPFilterRegion $region) {
            return $region->transformForApi();
        }, $this->regions);
        return $payload;
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'continents')) {
            $this->props['continents'] = array_map(
                function ($continent) {
                    return Continent::from($continent);
                },
                $data->continents
            );
        }
        if (property_exists($data, 'regions') && is_array($data->regions)) {
            $this->props['regions'] = array_map(function ($data) {
                return new IPFilterRegion($data);
            }, $data->regions);
        }
    }

    public function addContinent(Continent $continent): self
    {
        $this->addToCollection('continents', $continent);
        return $this;
    }

    public function removeContinent(Continent $continent): self
    {
        $this->removeFromCollection('continents', $continent);
        return $this;
    }

    public function addCountry(string $country): self
    {
        $this->addToCollection('countries', $country);
        return $this;
    }

    public function removeCountry(string $country): self
    {
        $this->removeFromCollection('countries', $country);
        return $this;
    }

    public function addASN(int $asn): self
    {
        $this->addToCollection('asn', $asn);
        return $this;
    }

    public function removeASN(int $asn): self
    {
        $this->removeFromCollection('asn', $asn);
        return $this;
    }

    public function addIPv4(string $ip): self
    {
        $this->addToCollection('ipv4', $ip);
        return $this;
    }

    public function removeIPv4(string $ip): self
    {
        $this->removeFromCollection('ipv4', $ip);
        return $this;
    }

    public function addIPv6(string $ip): self
    {
        $this->addToCollection('ipv6', $ip);
        return $this;
    }

    public function removeIPv6(string $ip): self
    {
        $this->removeFromCollection('ipv6', $ip);
        return $this;
    }

    public function addRegion(IPFilterRegion $region): self
    {
        $this->addToCollection('regions', $region);
        return $this;
    }

    public function removeRegion(IPFilterRegion $region): self
    {
        $this->removeFromCollection('regions', $region);
        return $this;
    }
}
