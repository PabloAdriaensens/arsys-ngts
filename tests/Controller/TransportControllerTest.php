<?php

namespace App\Tests\Infrastructure\Http;

use App\Application\Service\TransportDataService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransportControllerTest extends WebTestCase
{
    private $client;
    private const HTTP_OK = 200;
    private const HTTP_BAD_REQUEST = 400;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test that the index action returns a 200 status code when an origin is provided
     */
    public function testIndexReturns200WhenOriginIsProvided(): void
    {
        $this->client->request('GET', '/transport', ['origin' => 'Logroño']);
        $response = $this->client->getResponse();
        $this->assertSame(TransportDataService::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that the index action returns a 400 status code when no origin is provided
     */
    public function testIndexReturns400WhenOriginIsMissing(): void
    {
        $this->client->request('GET', '/transport');
        $response = $this->client->getResponse();
        $this->assertSame(TransportDataService::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test that the index action returns a valid JSON response
     */
    public function testIndexReturnsValidJson(): void
    {
        $this->client->request('GET', '/transport', ['origin' => 'Logroño']);
        $response = $this->client->getResponse();
        $this->assertSame(TransportDataService::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }
}
