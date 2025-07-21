<?php
declare(strict_types=1);

namespace App\Model;

class WeatherFormatter
{

    public function formatCurrentWeather(array $data): array
    {
        $currentDay = $data['days'][0] ?? [];

        return [
            'location' => [
                'name' => $data['resolvedAddress'] ?? $data['address'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'timezone' => $data['timezone'] ?? null,
            ],
            'current' => [
                'datetime' => $currentDay['datetime'] ?? null,
                'temperature' => $currentDay['temp'] ?? null,
                'feels_like' => $currentDay['feelslike'] ?? null,
                'humidity' => $currentDay['humidity'] ?? null,
                'wind_speed' => $currentDay['windspeed'] ?? null,
                'wind_direction' => $currentDay['winddir'] ?? null,
                'conditions' => $currentDay['conditions'] ?? null,
                'description' => $currentDay['description'] ?? null,
                'precipitation' => $currentDay['precip'] ?? null,
                'icon' => $currentDay['icon'] ?? null,
            ],
            'forecast_source' => 'Visual Crossing Weather API',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }


    public function formatForecast(array $data, int $days): array
    {
        $forecastDays = [];

        // Zpracování max. specifikovaného počtu dní nebo dostupných dní
        $daysCount = min($days, count($data['days'] ?? []));

        for ($i = 0; $i < $daysCount; $i++) {
            $day = $data['days'][$i] ?? [];

            $forecastDays[] = [
                'datetime' => $day['datetime'] ?? null,
                'temp_max' => $day['tempmax'] ?? null,
                'temp_min' => $day['tempmin'] ?? null,
                'feels_like' => $day['feelslike'] ?? null,
                'humidity' => $day['humidity'] ?? null,
                'wind_speed' => $day['windspeed'] ?? null,
                'wind_direction' => $day['winddir'] ?? null,
                'conditions' => $day['conditions'] ?? null,
                'description' => $day['description'] ?? null,
                'precipitation' => $day['precip'] ?? null,
                'precipitation_probability' => $day['precipprob'] ?? null,
                'icon' => $day['icon'] ?? null,
            ];
        }

        return [
            'location' => [
                'name' => $data['resolvedAddress'] ?? $data['address'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'timezone' => $data['timezone'] ?? null,
            ],
            'forecast' => $forecastDays,
            'forecast_source' => 'Visual Crossing Weather API',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}