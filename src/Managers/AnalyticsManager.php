<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Carbon\Carbon;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Models\Analytics;

/**
 * Manages Account Analytics
 * @package Constellix\Client\Managers
 */
class AnalyticsManager extends AbstractManager
{
    /**
     * The base URI for analytics.
     * @var string
     */
    protected string $baseUri = '/analytics';

    /**
     * Fetch the Analytics data for the account.
     * @param \DateTime $start
     * @param ?\DateTime $end
     * @return Analytics
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\ConstellixException
     * @throws \ReflectionException
     */

    public function get(\DateTime $start, ?\DateTime $end = null): Analytics
    {
        if ($end === null) {
            $end = Carbon::now();
        }

        $params = [
            'start' => $start->format('Ymd'),
            'end' => $end->format('Ymd'),
        ];

        $data = $this->client->get($this->getBaseUri(), $params);
        if (!$data) {
            throw new ConstellixException('No data returned from API');
        }
        $object = new Analytics($this, $this->client);
        $data = $this->transformApiData($data->data);
        $object->populateFromApi($data);
        $object->fullyLoaded = true;
        return $object;
    }
}
