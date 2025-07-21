<?php
declare(strict_types=1);

namespace App\Presentation;

use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Nette Weather API Documentation';
        $this->template->baseUrl = $this->getHttpRequest()->getUrl()->getBaseUrl();

        $this->template->apiInfo = [
            'description' => 'Simple API Wrapper for Visual Crossing Weather Api',
            'version' => '1.0.0',
            'source' => 'Visual Crossing Weather API',
            'source_url' => 'https://www.visualcrossing.com/weather-api',
        ];

        $this->template->parameters = [
            'units' => [
                'description' => 'Units of measurement',
                'values' => [
                    'metric' => 'Metric units (Celsius, km/h, mm)',
                    'us' => 'US units (Fahrenheit, mph, in)',
                    'uk' => 'UK units (Celsius, mph, mm)'
                ],
                'default' => 'metric'
            ],
            'lang' => [
                'description' => 'Language for text descriptions',
                'values' => [
                    'en' => 'English',
                    'cs' => 'Czech',
                    'de' => 'German',
                    'fr' => 'French',
                    'es' => 'Spanish',
                ],
                'default' => 'en'
            ]
        ];

        $this->template->rateLimit = [
            'limit' => 60,
            'window' => '1 minute',
            'identification' => 'by IP address',
            'headers' => [
                'X-RateLimit-Limit' => 'Maximum number of requests allowed',
                'X-RateLimit-Remaining' => 'Number of requests left for the time window',
                'X-RateLimit-Reset' => 'The time at which the current rate limit window resets in UTC epoch seconds',
                'Retry-After' => 'Seconds to wait before making a new request (only present when rate limit is exceeded)'
            ]
        ];

        $this->template->endpoints = [
            [
                'name' => 'Current Weather',
                'url' => 'api/current/{location}',
                'method' => 'GET',
                'description' => 'Get current weather for a specified location.',
                'parameters' => [
                    'location' => 'City name, postal code, or coordinates (required)',
                    'units' => 'Units of measurement (metric/us/uk, default: metric)',
                    'lang' => 'Language for text descriptions (en/cs/de/fr/es, default: en)'
                ],
                'examples' => [
                    'Basic' => 'api/current/Prague',
                    'With units' => 'api/current/New York?units=us',
                    'With language' => 'api/current/Berlin?lang=de',
                    'With all params' => 'api/current/Paris?units=metric&lang=fr'
                ],
                'response_example' => [
                    'status' => 'success',
                    'message' => 'Success',
                    'data' => [
                        'location' => [
                            'name' => 'Prague, Czech Republic',
                            'latitude' => 50.0755,
                            'longitude' => 14.4378,
                            'timezone' => 'Europe/Prague'
                        ],
                        'current' => [
                            'temperature' => 23.5,
                            'feels_like' => 24.2,
                            'humidity' => 65,
                            'wind_speed' => 12.3,
                            'conditions' => 'Partly Cloudy',
                            'description' => 'Partly cloudy throughout the day.'
                        ],
                        'forecast_source' => 'Visual Crossing Weather API',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]
            ],
            [
                'name' => 'Weather Forecast',
                'url' => 'api/forecast/{location}/{days}',
                'method' => 'GET',
                'description' => 'Get weather forecast for a specified location.',
                'parameters' => [
                    'location' => 'City name, postal code, or coordinates (required)',
                    'days' => 'Number of days for forecast (1-7, default: 5)',
                    'units' => 'Units of measurement (metric/us/uk, default: metric)',
                    'lang' => 'Language for text descriptions (en/cs/de/fr/es, default: en)'
                ],
                'examples' => [
                    'Basic' => 'api/forecast/Prague',
                    'With days' => 'api/forecast/Prague/3',
                    'With units' => 'api/forecast/New York/3?units=us',
                    'With language' => 'api/forecast/Berlin/3?lang=de',
                    'With all params' => 'api/forecast/Paris/3?units=metric&lang=fr'
                ],
                'response_example' => [
                    'status' => 'success',
                    'message' => 'Success',
                    'data' => [
                        'location' => [
                            'name' => 'Prague, Czech Republic',
                            'latitude' => 50.0755,
                            'longitude' => 14.4378,
                            'timezone' => 'Europe/Prague'
                        ],
                        'forecast' => [
                            [
                                'datetime' => date('Y-m-d'),
                                'temp_max' => 25.8,
                                'temp_min' => 18.2,
                                'conditions' => 'Partly Cloudy',
                                'description' => 'Partly cloudy throughout the day.'
                            ],
                            [
                                'datetime' => date('Y-m-d', strtotime('+1 day')),
                                'temp_max' => 24.5,
                                'temp_min' => 17.8,
                                'conditions' => 'Clear',
                                'description' => 'Clear conditions throughout the day.'
                            ]
                        ],
                        'forecast_source' => 'Visual Crossing Weather API',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]
            ]
        ];
    }
}