<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Client;
use Constellix\Client\Interfaces\ManagerInterface;
use Constellix\Client\Managers\AbstractManager;

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

    /**
     * Set a property on this object that is a reference to another object. This takes almost any input type and assigns
     * the correct object.
     * @param AbstractManager $manager
     * @param string $className
     * @param string $property
     * @param mixed $input
     * @return void
     */
    protected function setObjectReference(AbstractManager $manager, string $className, string $property, mixed $input): void
    {
        if ($input === null) {
            $this->props[$property] = null;
            $this->changed[] = $property;
            return;
        }

        if (is_integer($input)) {
            $input = new $className($manager, $this->client, (object)[
                'id' => $input,
            ]);
        }

        if ($input instanceof \stdClass) {
            $input = new $className($manager, $this->client, $input);
        }

        if ($input instanceof $className) {
            $this->props[$property] = $input;
            if (!in_array($property, $this->changed)) {
                $this->changed[] = $property;
            }
        }
    }
}
