<?php

namespace App\Application\Service;

use Symfony\Component\HttpFoundation\Response;

class TransportService
{
    /**
     * @param string $origin
     * @param $destination
     * @return array
     */
    public function getRouteData(string $origin, $destination): array
    {
        $cities = TransportDataService::CITIES;
        $connections = TransportDataService::CONNECTIONS;

        $validatedData = $this->validateData([$origin, $destination]);
        if (!empty($validatedData)) {
            return $validatedData;
        }

        $destination = empty($destination) ? $this->getRemainingCities($cities, $origin) : $destination;

        if (is_array($destination)) {
            foreach ($destination as $dest) {
                [$path, $totalCost] = $this->getCostCalculated($cities, $connections, $origin, $dest);
                $routeData[] = $this->formatResponse($path, $totalCost);
            }
        } else {
            [$path, $totalCost] = $this->getCostCalculated($cities, $connections, $origin, $destination);
            $routeData = $this->formatResponse($path, $totalCost);
        }

        return $routeData;
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
            if (!in_array($value, TransportDataService::CITIES, true)) {
                return [$value.' is not a valid city', Response::HTTP_BAD_REQUEST];
            }
        }

        return [];
    }

    /**
     * @param array $cities
     * @param array $connections
     * @param string $origin
     * @param $destination
     * @return array
     */
    public function getCostCalculated(array $cities, array $connections, string $origin, $destination): array
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
        $remainingCities = $this->getRemainingCities($cities, $origin);

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
     * @param $destination
     * @return array
     */
    public function getValidCombinationsForDestination(array $combinations, $destination): array
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
     * @param array $path
     * @param int $totalCost
     * @return array
     */
    public function formatResponse(array $path, int $totalCost): array
    {
        $section = [];
        foreach ($path as $element) {
            $section[] = [
                "origin" => $element["Origin"],
                "destination" => $element["Destination"],
                "cost" => $element["Cost"],
            ];
        }

        return [
            "route" => $section,
            "total_cost" => $totalCost
        ];
    }


    /**
     * @param array $cities
     * @param string $origin
     * @return array
     */
    public function getRemainingCities(array $cities, string $origin): array
    {
        return array_diff($cities, [$origin]);
    }
}
