<?php

namespace Constellix\Client\Tests\Unit\Model\DomainRecord;

use Constellix\Client\Client;
use Constellix\Client\Models\Domain;
use Constellix\Client\Tests\Unit\TestCase;
use GuzzleHttp\Psr7\Response;

abstract class RecordTestCase extends TestCase
{
    protected Client $api;
    protected Domain $domain;
    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getAuthenticatedClient();
        $this->mock->append(new Response(200, [], $this->getFixture('responses/domain/get.json')));
        $this->domain = $this->api->domains->get(366346);
    }
}
