<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Client;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\ManagerInterface;
use Constellix\Client\Models\AbstractModel;

/**
 * Abstract class for a resource manager.
 * @package Constellix\Client\Managers
 */
abstract class AbstractManager
{
    /**
     * The Constellix API Client.
     * @var Client
     */
    protected Client $client;

    /**
     * The URI for the resource.
     * @var string
     */
    protected string $baseUri;

    /**
     * A cache of objects fetched from the API.
     * @var AbstractModel[]
     */
    protected array $objectCache = [];

    /**
     * Fetches an object from the cache, or if not found, from the API.
     * @param int $id
     * @return AbstractModel
     * @throws ModelNotFoundException
     * @throws HttpException
     * @throws \ReflectionException
     */
    protected function getObject(int $id): AbstractModel
    {
        $objectId = $this->getObjectId($id);
        if ($this->getFromCache($objectId)) {
            return $this->getFromCache($objectId);
        }

        $data = $this->getFromApi($id);
        $object = $this->createExistingObject($data, $this->getModelClass());
        $object->fullyLoaded = true;
        return $object;
    }

    /**
     * Fetch a paginated subset of the resources. You can specify the page and the number of items per-page. The result
     * will be an object representing the paginated results. By specifying a custom Paginator Factory on the client
     * you can change the type of result you get from this method.
     *
     * By default this is a Paginator with a similar interface to the LengthAwarePaginator that is provided with
     * Laravel.
     *
     * @param int $page
     * @param int $perPage
     * @param array<mixed> $filters
     * @return Paginator|mixed
     * @throws HttpException
     */
    public function paginate(int $page = 1, int $perPage = 20, array $filters = [])
    {
        $params = $filters + [
                'page' => $page,
                'perPage' => $perPage,
            ];
        $data = $this->client->get($this->getBaseUri(), $params);
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }
        $items = array_map(
            function ($data) {
                $data = $this->transformApiData($data);
                return $this->createExistingObject($data, $this->getModelClass());
                ;
            },
            $data->data
        );

        return $this->client->getPaginatorFactory()->paginate($items, $data->meta->pagination->total, $perPage, $page);
    }

    /**
     * Get the object from the API.
     * @param int $id
     * @return \stdClass
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    protected function getFromApi(int $id): \stdClass
    {
        $uri = $this->getObjectUriFromId($id);
        try {
            $data = $this->client->get($uri);
        } catch (NotFoundException $e) {
            throw new ModelNotFoundException("Unable to find object with ID {$id}");
        }
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }
        return $this->transformApiData($data->data);
    }

    /**
     * Create a new object.
     * @param string|null $className
     * @return AbstractModel
     */
    protected function createObject(?string $className = null): AbstractModel
    {
        if (!$className) {
            $className = $this->getModelClass();
        }
        return new $className($this, $this->client);
    }

    /**
     * Deletes the object passed to it. If the object doesn't have an ID, no action is taken. The object is also
     * removed from the cache.
     * @param AbstractModel $object
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @internal
     */
    public function delete(AbstractModel $object): void
    {
        $idProperty = $this->getIdPropertyName();
        $id = $object->{$idProperty};
        if (!$id) {
            return;
        }
        $uri = $this->getObjectUri($object);
        $this->client->delete($uri);
        $this->removeFromCache($object);
    }

    /**
     * Saves the object passed to it.
     * @param AbstractModel $object
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @internal
     */
    public function save(AbstractModel $object): void
    {
        $idProperty = $this->getIdPropertyName();
        if ($object->{$idProperty}) {
            $data = $this->client->put($this->getObjectUri($object), $object->transformForApi());
            if (!$data) {
                throw new ConstellixException('No data returned from API');
            }
            $object->populateFromApi($data->data);
        } else {
            $data = $this->client->post($this->getBaseUri(), $object->transformForApi());
            if (!$data) {
                throw new ConstellixException('No data returned from API');
            }
            $object->populateFromApi($data->data, false);
        }
    }

    /**
     * Constructor for a Manager class.
     * @param Client $client
     * @internal
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetches the base URI for the resource.
     * @return string
     */
    protected function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * Fetches the URI for a resource with the specified ID.
     * @param AbstractModel $object
     * @return string
     */
    protected function getObjectUri(AbstractModel $object): string
    {
        $idPropertyName = $this->getIdPropertyName();
        $id = $object->{$idPropertyName};
        return "{$this->getBaseUri()}/{$id}";
    }

    /**
     * Fetches the URI for a resource with the specified ID.
     * @param int $id
     * @return string
     */
    protected function getObjectUriFromId(int $id): string
    {
        return "{$this->getBaseUri()}/{$id}";
    }

    /**
     * Return the name of the model class for this resource.
     * @return string
     * @throws \ReflectionException
     */
    protected function getModelClass(): string
    {
        $rClass = new \ReflectionClass($this);
        $modelName = substr($rClass->getShortName(), 0, -7);
        return '\Constellix\Client\Models\\' . $modelName;
    }

    /**
     * Returns a string ID to give a unique ID to this resource.
     * @param mixed $input
     * @param ?string $name
     * @return string
     * @throws \ReflectionException
     */
    protected function getObjectId(mixed $input, ?string $name = null): string
    {
        if ($name === null) {
            $name = $this->getModelClass();
        }

        $rClass = new \ReflectionClass($name);
        $name = $rClass->getShortName();
        $idPropertyName = $this->getIdPropertyName();
        if (is_object($input) && property_exists($input, $idPropertyName)) {
            $id = $input->{$idPropertyName};
            return "{$name}:{$id}";
        }
        return "{$name}:{$input}";
    }

    protected function getIdPropertyName(): string
    {
        return 'id';
    }

    /**
     * Creates a new instance of an object from existing API data.
     * @param \stdClass $data
     * @param string $className
     * @return AbstractModel
     * @throws \ReflectionException
     */
    protected function createExistingObject(\stdClass $data, string $className): AbstractModel
    {
        $objectId = $this->getObjectId($data, $className);
        if ($this->getFromCache($objectId)) {
            return $this->getFromCache($objectId);
        }

        $object = $this->createObject($className);
        $object->populateFromApi($data);

        $this->putInCache($objectId, $object);

        return $object;
    }

    /**
     * Fetch the object from the local cache.
     * @param string $key
     * @return ?AbstractModel
     * @internal
     */
    public function getFromCache(string $key): ?AbstractModel
    {
        if (array_key_exists($key, $this->objectCache)) {
            $this->client->logger->debug("[Constellix] Object Cache: Fetching {$key}");
            return $this->objectCache[$key];
        }
        return null;
    }

    /**
     * Put the object into the local cache.
     * @param string $key
     * @param AbstractModel $object
     * @internal
     */
    public function putInCache(string $key, AbstractModel $object): void
    {
        $this->client->logger->debug("[Constellix] Object Cache: Putting {$key}");
        $this->objectCache[$key] = $object;
    }

    /**
     * Remove the object from the local cache.
     * @param mixed $object
     * @internal
     */
    public function removeFromCache(mixed $object): void
    {
        if (is_object($object)) {
            $index = array_search($object, $this->objectCache);
            if ($index !== false) {
                $this->client->logger->debug("[Constellix] Object Cache: Removing {$index}");
                unset($this->objectCache[$index]);
            }
        } else {
            $index = (string)$object;
            $this->client->logger->debug("[Constellix] Object Cache: Removing {$index}");
            unset($this->objectCache[$index]);
        }
    }

    /**
     * Updates the object to the latest version in the API.
     * @param AbstractModel $object
     * @throws ModelNotFoundException
     * @internal
     */
    public function refresh(AbstractModel $object): void
    {
        if (!$object->id) {
            return;
        }

        $data = $this->getFromApi($object->id);
        $object->fullyLoaded = true;
        $object->populateFromApi($data);
    }

    /**
     * Applies transformations to the API data before it is used to instantiate a model.
     * @param \stdClass $data
     * @return \stdClass
     */
    protected function transformApiData(\stdClass $data): \stdClass
    {
        return $data;
    }
}
