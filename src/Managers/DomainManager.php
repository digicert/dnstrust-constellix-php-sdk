<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Carbon\Carbon;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\Domain;
use Constellix\Client\Models\DomainAnalytics;
use Constellix\Client\Pagination\Paginator;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages Domain API resources.
 * @package Constellix\Client\Managers
 */
class DomainManager extends AbstractManager
{
    use HasPagination {
        paginate as protected basePaginate;
    }

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains';

    /**
     * Create a new Domain.
     * @return Domain
     */
    public function create(): Domain
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing Domain.
     * @param int $id
     * @return Domain
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): Domain
    {
        return $this->getObject($id);
    }

    /**
     * Fetch a paginated subset of the resources. You can specify the page and the number of items per-page. The result
     * will be an object representing the paginated results. By specifying a custom Paginator Factory on the client
     * you can change the type of result you get from this method.
     *
     * By default this is a Paginator with a similar interface to the LengthAwarePaginator that is provided with
     * Laravel.
     *
     * As an optional filter in the third parameter, you can specify 'name' with a wildcard match (start and end) for
     * domains matching the search criteria. eg.
     *
     * $client->domains->paginate(1, 20, ['name' => '*example.com']);
     *
     * The domains returned through this are very shallow representations with just the ID and the name, so any attempt
     * to access more data about the domain will require another API request to fetch the full object.
     *
     * @param int $page
     * @param int $perPage
     * @param array<mixed> $filters
     * @return Paginator|mixed
     * @throws HttpException
     * @throws ConstellixException
     * @throws \ReflectionException
     */
    public function paginate(int $page = 1, int $perPage = 20, array $filters = [])
    {
        if (!array_key_exists('name', $filters)) {
            return $this->basePaginate($page, $perPage, $filters);
        }

        $params = $filters + [
            'page' => $page,
            'perPage' => $perPage,
        ];

        // We have a name in our filters, so we want to use the domain search endpoint
        $data = $this->client->get('/search/domains', $params);
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }
        $items = array_map(
            function ($data) {
                $data = $this->transformApiData($data);
                return $this->createExistingObject($data, Domain::class);
                ;
            },
            $data->data
        );

        return $this->client->getPaginatorFactory()->paginate($items, $data->meta->pagination->total, $perPage, $page);
    }

    /**
     * Fetch analytics for a domain and returns a DomainAnalytics object to represent them.
     *
     * @param Domain $domain
     * @param \DateTime $start
     * @param \DateTime|null $end
     * @return DomainAnalytics
     * @throws HttpException
     * @throws ConstellixException
     * @internal
     */
    public function getAnalytics(Domain $domain, \DateTime $start, ?\DateTime $end = null): DomainAnalytics
    {
        if ($end === null) {
            $end = Carbon::now();
        }
        $params = [
            'start' => $start->format('Ymd'),
            'end' => $end->format('Ymd'),
        ];
        $data = $this->client->get("/domains/{$domain->id}/analytics", $params);
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }

        $analytics = new DomainAnalytics();
        $analytics->setDomain($domain);
        $analytics->populateFromApi($data->data);
        $analytics->fullyLoaded = true;
        return $analytics;
    }
}
