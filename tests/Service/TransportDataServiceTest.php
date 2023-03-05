<?php

use App\Application\Service\TransportDataService;
use PHPUnit\Framework\TestCase;

class TransportDataServiceTest extends TestCase
{
    /**
     * Test that the $cities array is an array, has 9 elements and contains the 'Logroño' element
     */
    public function testCities(): void
    {
        $this->assertIsArray(TransportDataService::CITIES);
        $this->assertCount(9, TransportDataService::CITIES);
        $this->assertContainsEquals('Logroño', TransportDataService::CITIES);
    }

    /**
     * Test that the $connections array is a 2D array with 9 rows and 9 columns, and if the [0][0] element is 0
     */
    public function testConnections(): void
    {
        $this->assertIsArray(TransportDataService::CONNECTIONS);
        $this->assertCount(9, TransportDataService::CONNECTIONS);
        $this->assertCount(9, TransportDataService::CONNECTIONS[0]);
        $this->assertSame(0, TransportDataService::CONNECTIONS[0][0]);
    }

    /**
     * Test that each row of the $connections array has 9 elements and each element is an integer
     */
    public function testCityConnections(): void
    {
        foreach (TransportDataService::CONNECTIONS as $cityConnections) {
            $this->assertIsArray($cityConnections);
            $this->assertCount(9, $cityConnections);
            $this->assertContainsOnly('int', $cityConnections);
        }
    }

    /**
     * Test that all elements in the $connections array are non-negative
     */
    public function testNoNegativeConnections(): void
    {
        foreach (TransportDataService::CONNECTIONS as $cityConnections) {
            $this->assertGreaterThanOrEqual(0, min($cityConnections));
        }
    }
}
