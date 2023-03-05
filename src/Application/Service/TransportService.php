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
            return ['valid route'];
        }

        return $validateData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function validateData(array $data): array
    {
        foreach ($data as $value) {
            if (!in_array($value, TransportDataService::$cities, true)) {
                return ["$value is not a valid city", 400];
            }
        }

        return [];
    }

}