<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Client;
use Constellix\Client\Interfaces\ManagerInterface;
use Constellix\Client\Managers\AbstractManager;
use Constellix\Client\Managers\DomainHistoryManager;

trait ManagedModel
{
    /**
     * The Constellix API Client
     * @var Client
     */
    protected Client $client;

    /**
     * Creates the model and optionally populates it with data.
     * @param AbstractManager $manager
     * @param Client $client
     * @param ?\stdClass $data
     * @internal
     */
    public function __construct(AbstractManager $manager, Client $client, ?\stdClass $data = null)
    {
        $this->setInitialProperties();
        $this->manager = $manager;
        $this->client = $client;
        $this->originalProps = $this->props;
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    /**
     * Refresh the object with the representation from the API
     * @return void
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     */
    public function refresh(): void
    {
        $this->manager->refresh($this);
        // A refresh should fully load the object
        $this->fullyLoaded = true;
    }
}
