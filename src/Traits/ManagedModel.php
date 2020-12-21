<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;

trait ManagedModel
{
    /**
     * The manager for this object.
     * @var AbstractManagerInterface
     */
    protected $manager;

    /**
     * The Constellix API Client
     * @var ClientInterface
     */
    protected ClientInterface $client;

    /**
     * Creates the model and optionally populates it with data.
     * @param AbstractManagerInterface $manager
     * @param ClientInterface $client
     * @param object|null $data
     * @internal
     */
    public function __construct(AbstractManagerInterface $manager, ClientInterface $client, ?object $data = null)
    {
        $this->setInitialProperties();
        $this->manager = $manager;
        $this->client = $client;
        $this->originalProps = $this->props;
        if ($data) {
            $this->populateFromApi($data);
        }
    }
}