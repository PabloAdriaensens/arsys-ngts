<?php

namespace App\Application\Service;

class TransportService
{
    /**
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getRouteData(string $origin, string $destination): array
    {
        $data = [$origin, $destination];
        $validateData = $this->validateData($data);

        if (empty($validateData)) {
            [$path, $totalCost] = $this->getCostCalculated(
                TransportDataService::$cities,
                TransportDataService::$connections,
                $origin,
                $destination
            );

            return $this->formatResponse($origin, $destination, $path, $totalCost);
        }

        return $validateData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function validateData(array $data): array
    {

        foreach ($data as $key => $value) {
            if ($key === 1 && empty($value)) {
                continue;
            }
            if (!in_array($value, TransportDataService::$cities, true)) {
                return [$value.' is not a valid city', 400];
            }
        }

        return [];
    }

    /**
     * @param array $cities
     * @param array $connections
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getCostCalculated(array $cities, array $connections, string $origin, string $destination): array
    {
        $combinations = $this->getCombinations($cities, $origin);
        $combinations = $this->getValidCombinationsForDestination($combinations, $destination);

        return $this->getLowestCostRoute($combinations, $cities, $connections);
    }

    /**
     * @param array $cities
     * @param string $origin
     * @return array
     */
    public function getCombinations(array $cities, string $origin = ""): array
    {
        $remainingCities = array_diff($cities, [$origin]);

        if (count($remainingCities) <= 1) {
            return $remainingCities;
        }

        $combinations = [];
        foreach ($remainingCities as $city) {
            $rest = array_diff($remainingCities, [$city]);
            $subCombinations = $this->getCombinations($rest);
            foreach ($subCombinations as $subCombination) {
                $combinations[] = $city.$subCombination;
            }
        }

        $prefixedCombinations = [];
        foreach ($combinations as $combination) {
            $prefixedCombinations[] = $origin."_".$combination;
        }

        return $prefixedCombinations;
    }

    /**
     * @param array $combinations
     * @param string $destination
     * @return array
     */
    public function getValidCombinationsForDestination(array $combinations, string $destination): array
    {
        $validCombinations = [];

        foreach ($combinations as $combination) {
            $cities = explode("_", $combination);
            $destinationIndex = array_search($destination, $cities, true);

            if ($destinationIndex !== false) {
                $tempCombination = array_slice($cities, 0, $destinationIndex + 1);
                $tempCombinationKey = implode('_', $tempCombination);

                if (!isset($validCombinations[$tempCombinationKey])) {
                    $validCombinations[$tempCombinationKey] = $tempCombination;
                }
            }
        }

        return array_values($validCombinations);
    }

    /**
     * @param array $combinations
     * @param array $cities
     * @param array $connections
     * @return array
     */
    public function getLowestCostRoute(array $combinations, array $cities, array $connections): array
    {
        $lowestCostRoute = [];
        $lowestCost = null;
        $cityIndex = array_flip($cities);

        foreach ($combinations as $combination) {
            $currentCombination = [];
            $currentCost = 0;
            $prevCity = null;

            foreach ($combination as $currentCity) {
                if ($prevCity !== null) {
                    $startCityIndex = $cityIndex[$prevCity];
                    $endCityIndex = $cityIndex[$currentCity];
                    $connectionCost = $connections[$startCityIndex][$endCityIndex];

                    if ($connectionCost === 0) {
                        // If the connection cost is zero, skip to the next combination.
                        continue 2;
                    }

                    $currentCombination[] = [
                        'Origin' => $prevCity,
                        'Destination' => $currentCity,
                        'Cost' => $connectionCost,
                    ];

                    $currentCost += $connectionCost;
                }

                $prevCity = $currentCity;
            }

            if ($lowestCost === null || $currentCost < $lowestCost) {
                $lowestCostRoute = $currentCombination;
                $lowestCost = $currentCost;
            }
        }

        return [$lowestCostRoute, $lowestCost];
    }

    /**
     * @param string $origin
     * @param string $destination
     * @param array $path
     * @param int $totalCost
     * @return array
     */
    public function formatResponse(string $origin, string $destination, array $path, int $totalCost): array
    {
        $route = "The cheapest route for a shipment between $origin and $destination is:";

        $section = [];
        foreach ($path as $element) {
            $section[] = $element["Origin"]." - ".$element["Destination"]." section, with a cost of: ".$element["Cost"];
        }

        $cost = "With a total cost of: $totalCost";

        return array($route, $section, $cost);
    }
}
