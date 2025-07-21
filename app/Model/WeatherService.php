<?php
declare(strict_types=1);

namespace App\Model;



use GuzzleHttp\Client;
use Nette\Caching\Storage;
use Nette\Caching\Cache;
use Nette\Utils\Json;

class WeatherService
{
    private const BASE_URL = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/';
    private Client $httpClient;
    private string $apiKey;
    private ?Cache $cache;

    public function __construct(string $apiKey, ?Storage $storage = null)
    {
        $this->httpClient = new Client();
        $this->apiKey = $apiKey;
        $this->cache = $storage ? new Cache($storage, 'weatherapi') : null;
    }

    /**
     * Získá současné počasí pro $location
     *
     * @param string $location
     *
     * @return array
     */
    public function getCurrentWeather(string $location): array
    {
        try {
            $cacheKey = md5($location);

            file_put_contents(__DIR__ . '/../../log/debug.log',
                "Getting weather for: $location\n" .
                "API Key: " . (empty($this->apiKey) ? 'EMPTY!' : 'OK') . "\n",
                FILE_APPEND);

            if ($this->cache !== null) {
                $data = $this->cache->load($cacheKey);
                if ($data !== null) {
                    return $data;
                }
            }

            $url = self::BASE_URL . urlencode($location) . '/today';
            $response = $this->makeRequest($url);

            if ($this->cache !== null) {
                $dependencies = [
                    Cache::Expire => '30 minutes',
                ];
                $this->cache->save($cacheKey, $response, $dependencies);
            }

            return $response;
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../log/debug.log',
                "Error in getCurrentWeather: " . $e->getMessage() . "\n" .
                $e->getTraceAsString() . "\n",
                FILE_APPEND);

            throw  $e; // preposli vyjimku
        }
    }


    public function getForecast(string $location, int $days = 5): array
    {
        $cacheKey = md5($location . '_' . $days);

        // naber z cache, pokud to jde
        if ($this->cache !== null) {
            $data = $this->cache->load($cacheKey);
            if ($data !== null) {
                return $data;
            }
        }

        // podle dokumentace https://www.visualcrossing.com/resources/documentation/weather-api/using-the-time-period-parameter-to-specify-dynamic-dates-for-weather-api-requests/
        // použijeme nextXdays
        $url = self::BASE_URL . urlencode($location) . '/next' . $days . 'days';
        $response = $this->makeRequest($url);

        // ulozi do cache pokud ji ma
        if ($this->cache !== null) {
            $dependencies = [
                Cache::Expire => '2 hours',
            ];
            $this->cache->save($cacheKey, $response, $dependencies);
        }

        return $response;
    }

    private function makeRequest(string $url): array
    {
        $params = [
            'query' => [
                'key' => $this->apiKey,
                'unitGroup' => 'metric',    // Metrické jednotky (teploty v celsiu atd.
                'include' => 'days',        // Zahrnout do odpovědi denní data
                'contentType' => 'json',    // Formát odpovědi
            ],
        ];

        $response = $this->httpClient->get($url, $params);
        $data = Json::decode((string) $response->getBody(), forceArrays: true);

        return $data;
    }
}