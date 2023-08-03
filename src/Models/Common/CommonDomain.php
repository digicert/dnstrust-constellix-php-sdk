<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Managers\DomainHistoryManager;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Managers\DomainRecordManager;
use Constellix\Client\Managers\DomainSnapshotManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Domain resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read DomainSnapshotManager $snapshots
 * @property-read DomainHistoryManager $history
 * @property-read DomainRecordManager $records
 */
abstract class CommonDomain extends AbstractModel
{
    use ManagedModel;

    protected DomainManager $manager;
    protected ?DomainSnapshotManager $snapshots = null;
    protected ?DomainHistoryManager $history = null;
    protected ?DomainRecordManager $records = null;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
    ];

    protected function getRecords(): DomainRecordManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access records');
        }
        if ($this->records === null) {
            $this->records = new DomainRecordManager($this->client);
            $this->records->setDomain($this);
        }
        return $this->records;
    }

    protected function getHistory(): DomainHistoryManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access history');
        }
        if ($this->history === null) {
            $this->history = new DomainHistoryManager($this->client);
            $this->history->setDomain($this);
        }
        return $this->history;
    }

    protected function getSnapshots(): DomainSnapshotManager
    {
        if (!$this->id) {
            throw new ConstellixException('Domain must be created before you can access snapshots');
        }
        if ($this->snapshots === null) {
            $this->snapshots = new DomainSnapshotManager($this->client);
            $this->snapshots->setDomain($this);
        }
        return $this->snapshots;
    }
}
