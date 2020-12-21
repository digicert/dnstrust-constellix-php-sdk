<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;

/**
 * Abstract class for a resource manager.
 * @package Constellix\Client\Managers
 */
abstract class AbstractManager implements AbstractManagerInterface
{
    /**
     * The Constellix API Client.
     * @var ClientInterface
     */
    protected ClientInterface $client;

    /**
     * The URI for the resource.
     * @var string
     */
    protected string $baseUri;

    /**
     * A cache of objects fetched from the API.
     * @var AbstractModelInterface[]
     */
    protected array $objectCache = [];

    /**
     * Fetches an object from the cache, or if not found, from the API.
     * @param int $id
     * @return AbstractModelInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     * @throws \ReflectionException
     */
    protected function getObject(int $id): AbstractModelInterface
    {
        $objectId = $this->getObjectId($id);
        if ($this->getFromCache($objectId)) {
            return $this->getFromCache($objectId);
        }

        $data = $this->getFromApi($id);
        return $this->createExistingObject($data, $this->getModelClass());
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
     * @param array|null $filters
     * @return Paginator|mixed
     * @throws HttpException
     */
    public function paginate(int $page = 1, int $perPage = 20, $filters = [])
    {
        $params = $filters + [
                'page' => $page,
                'per_page' => $perPage,
            ];
        $data = $this->client->get($this->getBaseUri(), $params);
        $items = array_map(
            function ($data) {
                $data = $this->transformConciseApiData($data);
                return $this->createExistingObject($data, $this->getConciseModelClass());;
            },
            $data->data
        );

        $hasMorePages = (bool) $data->meta->links->next;
        return $this->client->getPaginatorFactory()->paginate($items, $data->meta->pagination->total, $perPage, $page);
    }

    /**
     * Get the object from the API.
     * @param int $id
     * @return object
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    protected function getFromApi(int $id): object
    {
        $uri = $this->getObjectUriFromId($id);
        try {
            $data = $this->client->get($uri);
        } catch (NotFoundException $e) {
            throw new ModelNotFoundException("Unable to find object with ID {$id}");
        }
        return $this->transformApiData($data->data);
    }

    /**
     * Create a new object.
     * @param string|null $className
     * @return AbstractModelInterface
     */
    protected function createObject(?string $className = null): AbstractModelInterface
    {
        if (!$className) {
            $className = $this->getModelClass();
        }
        return new $className($this, $this->client);
    }

    /**
     * Deletes the object passed to it. If the object doesn't have an ID, no action is taken. The object is also
     * removed from the cache.
     * @param AbstractModelInterface $object
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @internal
     */
    public function delete(AbstractModelInterface $object): void
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
     * @param AbstractModelInterface $object
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @internal
     */
    public function save(AbstractModelInterface $object): void
    {
        $idProperty = $this->getIdPropertyName();
        if ($object->{$idProperty}) {
            $data = $this->client->post($this->getObjectUri($object), $object->transformForApi());
            $object->populateFromApi($data->data);
        } else {
            $data = $this->client->post($this->getBaseUri(), $object->transformForApi());
            $object->populateFromApi($data->data, false);
        }
    }

    /**
     * Constructor for a Manager class.
     * @param ClientInterface $client
     * @internal
     */
    public function __construct(ClientInterface $client)
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
     * @param AbstractModelInterface $object
     * @return string
     */
    protected function getObjectUri(AbstractModelInterface $object): string
    {
        $idPropertyName = $this->getIdPropertyName();
        if (!$object->{$idPropertyName}) {
            throw new ConstellixException("No {$idPropertyName} available on object");
        }
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
     * Return the name of the model class for the concise version of the resource.
     * @return string
     * @throws \ReflectionException
     */
    protected function getConciseModelClass(): string
    {
        return $this->getModelClass();
    }

    /**
     * Returns a string ID to give a unique ID to this resource.
     * @param $input
     * @param string|null $name
     * @return string
     * @throws \ReflectionException
     */
    protected function getObjectId($input, string $name = null)
    {
        if ($name === null) {
            $name = $this->getModelClass();
        }

        $rClass = new \ReflectionClass($name);
        $name = $rClass->getShortName();
        $idPropertyName = $this->getIdPropertyName();
        if (is_scalar($input)) {
            return "{$name}:{$input}";
        } elseif (is_object($input) && property_exists($input, $idPropertyName)) {
            $id = $input->{$idPropertyName};
            return "{$name}:{$id}";
        } elseif (is_array($input) && array_key_exists($input, $idPropertyName)) {
            return "{$name}:{$input[$idPropertyName]}";
        }
        return "{$name}:" . (string)$input;
    }

    protected function getIdPropertyName()
    {
        return 'id';
    }

    /**
     * Creates a new instance of an object from existing API data.
     * @param object $data
     * @param string $className
     * @return AbstractModelInterface
     * @throws \ReflectionException
     */
    protected function createExistingObject(object $data, string $className): AbstractModelInterface
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
     * @param $key
     * @return AbstractModelInterface
     * @internal
     */
    public function getFromCache($key)
    {
        if (array_key_exists($key, $this->objectCache)) {
            $this->client->logger->debug("[Constellix] Object Cache: Fetching {$key}");
            return $this->objectCache[$key];
        }
    }

    /**
     * Put the object into the local cache.
     * @param $key
     * @param $object
     * @internal
     */
    public function putInCache($key, $object)
    {
        $this->client->logger->debug("[Constellix] Object Cache: Putting {$key}");
        $this->objectCache[$key] = $object;
    }

    /**
     * Remove the object from the local cache.
     * @param $object
     * @internal
     */
    public function removeFromCache($object)
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
     * @param AbstractModelInterface $object
     * @throws ModelNotFoundException
     * @internal
     */
    public function refresh(AbstractModelInterface $object): void
    {
        if (!$object->id) {
            return;
        }

        $data = $this->getFromApi($object->id);
        $object->populateFromApi($data);
    }

    /**
     * Applies transformations to the API data before it is used to instantiate a model.
     * @param object $data
     * @return object
     */
    protected function transformApiData(object $data): object
    {
        return $data;
    }

    /**
     * Applies transformations to the concise API data before it is used to instantiate a model.
     * @param object $data
     * @return object
     */
    protected function transformConciseApiData(object $data): object
    {
        return $this->transformApiData($data);
    }
}