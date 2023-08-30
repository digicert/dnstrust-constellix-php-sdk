<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\GTDLocation;
use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Models\Helpers\RecordValue;
use Constellix\Client\Models\Helpers\RecordValues\CAA;
use Constellix\Client\Models\Helpers\RecordValues\CERT;
use Constellix\Client\Models\Helpers\RecordValues\Failover;
use Constellix\Client\Models\Helpers\RecordValues\HINFO;
use Constellix\Client\Models\Helpers\RecordValues\HttpRedirection;
use Constellix\Client\Models\Helpers\RecordValues\MX;
use Constellix\Client\Models\Helpers\RecordValues\NAPTR;
use Constellix\Client\Models\Helpers\RecordValues\NS;
use Constellix\Client\Models\Helpers\RecordValues\Pool as PoolRecordValue;
use Constellix\Client\Models\Helpers\RecordValues\PTR;
use Constellix\Client\Models\Helpers\RecordValues\RoundRobinFailover;
use Constellix\Client\Models\Helpers\RecordValues\RP;
use Constellix\Client\Models\Helpers\RecordValues\SPF;
use Constellix\Client\Models\Helpers\RecordValues\SRV;
use Constellix\Client\Models\Helpers\RecordValues\Standard;
use Constellix\Client\Models\Helpers\RecordValues\TXT;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Record resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property RecordType $type
 * @property int $ttl
 * @property RecordMode $mode;
 * @property ?GTDLocation $region;
 * @property ?IPFilter $ipfilter;
 * @property ?GeoProximity $geoproximity;
 * @property bool $geoFailover;
 * @property bool $ipfilterDrop;
 * @property bool $enabled;
 * @property ?string $notes;
 * @property ?bool $skipLookup;
 * @property ContactList[] $contacts;
 * @property mixed $value;
 * @property \stdClass $lastValues
 */
abstract class Record extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'type' => null,
        'ttl' => null,
        'mode' => null,
        'region' => null,
        'ipfilter' => null,
        'geoFailover' => null,
        'ipfilterDrop' => null,
        'geoproximity' => null,
        'enabled' => null,
        'value' => null,
        'lastValues' => null,
        'notes' => null,
        'contacts' => [],
        'skipLookup' => null,

    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'type',
        'ttl',
        'mode',
        'region',
        'ipfilter',
        'geoFailover',
        'ipfilterDrop',
        'geoproximity',
        'enabled',
        'value',
        'notes',
        'contacts',
        'skipLookup',
    ];

    protected function setInitialProperties(): void
    {
        $this->props['mode'] = RecordMode::STANDARD();
    }

    /**
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        unset(
            $data->domain,
            $data->template,
        );
        $data->mode = RecordMode::from($data->mode);
        $data->type = RecordType::from($data->type);

        parent::parseApiData($data);

        $this->props['mode'] = $data->mode;

        $lastValues = new \stdClass();
        foreach ($data->lastValues as $mode => $lastValue) {
            $mode = RecordMode::from($mode);
            $lastValues->{$mode->value} = $this->parseValue($this->props['type'], $mode, $lastValue);
        }
        $this->props['lastValues'] = $lastValues;
        $this->props['value'] = $lastValues->{$this->props['mode']->value};

        if (property_exists($data, 'ipfilter') && $data->ipfilter) {
            $this->props['ipfilter'] = new IPFilter($this->client->ipfilters, $this->client, $data->ipfilter);
        }
        if (property_exists($data, 'region') && $data->region) {
            $this->props['region'] = GTDLocation::from($data->region);
        }
        if (property_exists($data, 'geoproximity') && $data->geoproximity) {
            $this->props['geoproximity'] = new GeoProximity($this->client->geoproximity, $this->client, $data->geoproximity);
        }
        if (property_exists($data, 'contacts') && $data->contacts) {
            $this->props['contacts'] = array_map(function ($data) {
                return new ContactList($this->client->contactlists, $this->client, $data);
            }, $data->contacts);
        }
    }

    /**
     * @param RecordType $type
     * @param RecordMode $mode
     * @param mixed $data
     * @return mixed
     */
    protected function parseValue(RecordType $type, RecordMode $mode, mixed $data): mixed
    {
        // Special case - this is not an array of values
        if ($type === RecordType::HTTP()) {
            return new HttpRedirection($data);
        }

        switch ($type) {
            case RecordType::A():
                // Intentionally continuing
            case RecordType::AAAA():
                // A and AAAA have RoundRobinFailover as modes
                if ($mode === RecordMode::ROUNDROBINFAILOVER()) {
                    return array_map(function ($value) {
                        return new RoundRobinFailover($value);
                    }, $data);
                }
                // Intentionally continuing
                // no break
            case RecordType::CNAME():
                // Intentionally continuing
            case RecordType::ANAME():
                switch ($mode) {
                    case RecordMode::STANDARD():
                        return array_map(function ($value) {
                            return new Standard($value);
                        }, $data);

                    case RecordMode::POOLS():
                        return array_map(function ($value) {
                            $matches = [];
                            preg_match('/\/pools\/(?<type>.*)\/\d+$/', $value->links->self, $matches);
                            $value = (object)[
                                'id' => $value->id,
                                'name' => $value->name ?? null,
                                'type' => $matches['type'],
                            ];
                            return new PoolRecordValue((object)[
                                'pool' => new Pool($this->client->pools, $this->client, $value),
                            ]);
                        }, $data);

                    case RecordMode::FAILOVER():
                        return new Failover($data);
                }
                // Intentionally continuing
                // no break
            default:
                break;
        }

        $classMap = [
            RecordType::CAA()->value => CAA::class,
            RecordType::CERT()->value => CERT::class,
            RecordType::HINFO()->value => HINFO::class,
            RecordType::MX()->value => MX::class,
            RecordType::NAPTR()->value => NAPTR::class,
            RecordType::NS()->value => NS::class,
            RecordType::PTR()->value => PTR::class,
            RecordType::RP()->value => RP::class,
            RecordType::SPF()->value => SPF::class,
            RecordType::SRV()->value => SRV::class,
            RecordType::TXT()->value => TXT::class,
        ];
        return array_map(function ($value) use ($type, $classMap) {
            $className = $classMap[$type->value];
            return new $className($value);
        }, $data);
    }

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();

        if (is_array($this->value)) {
            $payload->value = array_map(function ($value) {
                return $value->transformForApi();
            }, $this->value);
        } elseif ($this->value) {
            $payload->value = $this->value->transformForApi();
        }
        unset(
            $payload->lastValues
        );

        $payload->ipfilter = $this->ipfilter->id ?? null;
        $payload->geoproximity = $this->geoproximity->id ?? null;
        $payload->contacts = array_map(function ($contact) {
            /**
             * @var \stdClass $contact
             */
            return $contact->id;
        }, $this->contacts);

        return $payload;
    }

    public function setType(RecordType $type): void
    {
        if ($this->id) {
            throw new ReadOnlyPropertyException('Unable to set type on a record that has been created');
        }
        $this->props['type'] = $type;
        $this->changed[] = 'type';
    }

    public function setValue(mixed $recordValue): void
    {
        $this->changed[] = 'mode';
        $this->changed[] = 'value';

        if ($recordValue instanceof HttpRedirection) {
            $this->props['mode'] = RecordMode::STANDARD();
            $this->props['value'] = $recordValue;
        } elseif ($recordValue instanceof Failover) {
            $this->props['mode'] = RecordMode::FAILOVER();
            $this->props['value'] = $recordValue;
        } elseif (is_array($recordValue)) {
            $this->props['value'] = $recordValue;
            $this->props['mode'] = RecordMode::STANDARD();
            if ($recordValue && $recordValue[0] instanceof RoundRobinFailover) {
                $this->props['mode'] = RecordMode::ROUNDROBINFAILOVER();
            } elseif ($recordValue && $recordValue[0] instanceof PoolRecordValue) {
                $this->props['mode'] = RecordMode::POOLS();
            }
        } else {
            $this->setValue([$recordValue]);
        }
    }

    public function addValue(RecordValue $recordValue): void
    {
        if (!$this->value) {
            $this->setValue($recordValue);
        } else {
            $values = $this->value;
            $values[] = $recordValue;
            $this->value = $values;
        }
    }

    public function addContactList(ContactList $contactList): self
    {
        $this->addToCollection('contacts', $contactList);
        return $this;
    }

    public function removeContactList(ContactList $contactList): self
    {
        $this->removeFromCollection('contacts', $contactList);
        return $this;
    }

    public function setIPFilter(null|int|\stdClass|IPFilter $ipfilter): void
    {
        $this->setObjectReference($this->client->ipfilters, IPFilter::class, 'ipfilter', $ipfilter);
    }

    public function setGeoProximity(null|int|\stdClass|GeoProximity $geoproximity): void
    {
        $this->setObjectReference($this->client->geoproximity, GeoProximity::class, 'geoproximity', $geoproximity);
    }
}
