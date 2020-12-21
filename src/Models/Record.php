<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Records\RecordMode;
use Constellix\Client\Enums\Records\RecordType;
use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Models\RecordInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Models\Basic\BasicPool;
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
 */
abstract class Record extends AbstractModel implements RecordInterface, EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected array $props = [
        'name' => null,
        'type' => null,
        'ttl' => null,
        'mode' => null,
        'region' => null,
        'ipfilter' => null,
        'ipfilterDrop' => null,
        'geoproximity' => null,
        'enabled' => null,
        'value' => null,
        'lastValues' => [],
        'notes' => null,
        'contacts' => [],

    ];

    protected array $editable = [
        'name',
        'type',
        'ttl',
        'mode',
        'region',
        'ipfilter',
        'ipfilterDrop',
        'geoproximity',
        'enabled',
        'value',
        'notes',
        'contacts',
    ];

    protected function setInitialProperties()
    {
        $this->props['mode'] = RecordMode::STANDARD();
    }

    protected function parseApiData(object $data): void
    {
        unset(
            $data->domain,
            $data->template,
        );
        $data->type = RecordType::make($data->type);
        $data->mode = RecordMode::make($data->mode);

        parent::parseApiData($data);

        $lastValues = [];
        foreach ($data->lastValues as $mode => $lastValue) {
            $mode = RecordMode::make($mode);
            $lastValues[$mode->value] = $this->parseValue($this->props['type'], $mode, $lastValue);
        }
        $this->props['lastValues'] = $lastValues;
        $this->props['value'] = $lastValues[$this->props['mode']->value];
    }

    protected function parseValue(RecordType $type, RecordMode $mode, $data)
    {
        // Special case - this is not an array of values
        if ($type == RecordType::HTTP()) {
            return new HttpRedirection($data);
        }

        switch ($type) {
            case RecordType::A():
            case RecordType::AAAA():
                // A and AAAA have RoundRobinFailover as modes
                if ($mode == RecordMode::ROUNDROBINFAILOVER()) {
                    return array_map(function($value) {
                        return new RoundRobinFailover($value);
                    }, $data);
                }
                // Intentionally continuing
            case RecordType::CNAME():
            case RecordType::ANAME():
                switch ($mode) {
                    case RecordMode::STANDARD():
                        return array_map(function($value) {
                            return new Standard($value);
                        }, $data);

                    case RecordMode::POOLS():
                        return array_map(function($value) {
                            return new PoolRecordValue((object) [
                                'pool' => new BasicPool($this->client->pools, $this->client, $value),
                            ]);
                        }, $data);

                    case RecordMode::FAILOVER():
                        return new Failover($data);
                }
            default:
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
                if (array_key_exists($type->value, $classMap)) {
                    return array_map(function($value) use ($type, $classMap) {
                        $className = $classMap[$type->value];
                        return new $className($value);
                    }, $data);
                }
                break;
        }
        return null;
    }

    public function transformForApi(): object
    {
        $payload = parent::transformForApi(); // TODO: Change the autogenerated stub
        if (is_array($this->value)) {
            $payload->value = array_map(function($value) {
                return $value->transformForApi();
            }, $this->value);
        } elseif ($this->value) {
            $payload->value = $this->value->transformForApi();
        }
        unset(
            $payload->lastValues
        );
        return $payload;
    }

    public function setType(RecordType $type)
    {
        if ($this->id) {
            throw new ReadOnlyPropertyException('Unable to set type on a record that has been created');
        }
        $this->props['type'] = $type;
        $this->changed[] = 'type';
    }

    public function setValue($recordValue)
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

    public function addValue(RecordValue $recordValue)
    {
        if (!$this->value) {
            $this->setValue($recordValue);
        } else {
            $values = $this->value;
            $values[] = $recordValue;
            $this->values = $values;
        }
    }
}