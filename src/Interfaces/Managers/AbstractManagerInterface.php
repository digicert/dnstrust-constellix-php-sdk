<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use Constellix\Client\Pagination\Paginator;

/**
 * Defines the interface of a Manager for a particular resource in the Constellix API.
 *
 * The manager is the way that resources are fetched, queried and updated in the SDK. There should be one for every
 * resource that can be manipulated.
 *
 * @package Constellix\Client\Interfaces
 */
interface AbstractManagerInterface
{
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
    public function paginate(int $page = 1, int $perPage = 20, $filters = []);

    /**
     * Updates the API with changes made to the specified object. If the object is new, it will be created.
     * @param AbstractModelInterface $object
     * @throws HttpException
     * @internal
     */
    public function save(AbstractModelInterface $object): void;

    /**
     * Uses the API to delete the specified object. If the object is new, then no action is taken on the API.
     * @param AbstractModelInterface $object
     * @throws HttpException
     * @internal
     */
    public function delete(AbstractModelInterface $object): void;

    /**
     * Fetch the object from the local cache.
     * @param $key
     * @return AbstractModelInterface
     * @internal
     */
    public function getFromCache($key);

    /**
     * Put the object into the local cache.
     * @param $key
     * @param $object
     * @internal
     */
    public function putInCache($key, $object);

    /**
     * Remove the object from the local cache.
     * @param $object
     * @internal
     */
    public function removeFromCache($object);
}