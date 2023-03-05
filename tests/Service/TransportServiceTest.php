<?php

use App\Application\Service\TransportService;
use PHPUnit\Framework\TestCase;

class TransportServiceTest extends TestCase
{
    public const INVALID_CITY = 'Barcelona';
    private TransportService $apiService;

    protected function setUp(): void
    {
        $this->apiService = new TransportService();
    }

    /**
     * Test to validate that validateData() returns an error for invalid city
     */
    public function testValidateDataReturnsErrorForInvalidCity(): void
    {
        $data = [self::INVALID_CITY, "Logroño"];
        $expectedResult = [self::INVALID_CITY.' is not a valid city', 400];

        $this->assertSame($expectedResult, $this->apiService->validateData($data));

        $data = ['Logroño', self::INVALID_CITY];
        $this->assertSame($expectedResult, $this->apiService->validateData($data));
    }

    /**
     * Test to validate that validateData() returns no error for valid city
     */
    public function testValidateDataReturnsNoErrorForValidCity(): void
    {
        $data = ['Logroño', 'Zaragoza'];

        $this->assertEmpty($this->apiService->validateData($data));
    }

    /**
     * Test to validate that getRouteData() returns an error for invalid data
     */
    public function testGetRouteDataReturnsErrorForInvalidData(): void
    {
        $result = $this->apiService->getRouteData('Logroño', self::INVALID_CITY);
        $this->assertSame([self::INVALID_CITY.' is not a valid city', 400], $result);

        $result = $this->apiService->getRouteData(self::INVALID_CITY, 'Logroño');
        $this->assertSame([self::INVALID_CITY.' is not a valid city', 400], $result);
    }

    /**
     * Test to validate that getRouteData() returns valid data
     */
    public function testGetRouteDataReturnsValidData(): void
    {
        $result = $this->apiService->getRouteData('Logroño', '');
        $this->assertGreaterThan(1, count($result));
    }
}
