<?php

namespace Constellix\Client\Traits;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Pagination\Paginator;

/**
 * Implements pagination in AbstractManager objects.
 */
trait HasPagination
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
}
