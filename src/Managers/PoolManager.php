<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Models\Pool;

/**
 * Manages Pool Resources
 * @package Constellix\Client\Managers
 */
class PoolManager extends AbstractManager
{
    /**
     * The base URI for resources.
     * @var string
     */
    protected string $baseUri = '/pools';

    public function create(): Pool
    {
        return $this->createObject();
    }

    protected function getObjectId(mixed $input, ?string $name = null)
    {
        $input = (array)$input;
        return "Pool:{$input['type']}:{$input['id']}";
    }

    public function get(PoolType $type, int $id): Pool
    {
        $objectId = $this->getObjectId(['type' => $type->value, 'id' => $id]);
        if ($this->getFromCache($objectId)) {
            return $this->getFromCache($objectId);
        }

        $data = $this->getPoolFromApi($type, $id);
        $object = $this->createExistingObject($data, $this->getModelClass());
        $object->fullyLoaded = true;
        return $object;
    }

    public function refresh(AbstractModel $object): void
    {
        /**
         * @var Pool $object
         */
        if ($object->id === null || $object->type === null) {
            return;
        }

        $data = $this->getPoolFromApi($object->type, $object->id);
        $object->fullyLoaded = true;
        $object->populateFromApi($data);
    }

    /**
     * Fetches the URI for a resource with the specified ID.
     * @param Pool $object
     * @return string
     */
    protected function getObjectUri(AbstractModel $object): string
    {
        if ($object->id === null || $object->type === null) {
            throw new ConstellixException('No ID or Type available on object');
        }
        $type = strtolower((string)$object->type->value);
        return "{$this->getBaseUri()}/{$type}/{$object->id}";
    }

    protected function getPoolFromApi(PoolType $type, int $id): \stdClass
    {
        $lowerType = strtolower((string)$type->value);
        $uri = "{$this->getBaseUri()}/{$lowerType}/{$id}";
        try {
            $data = $this->client->get($uri);
        } catch (NotFoundException $e) {
            throw new ModelNotFoundException("Unable to find object with Type {$type->value} and ID {$id}");
        }
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }
        return $this->transformApiData($data->data);
    }
}
