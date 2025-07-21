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
     * Získá současné počasí pro $location s možnostmi
     *
     * @param string $location Lokace (město, PSČ, souřadnice...)
     * @param array $options Doplnující možnosti pro API dotazy
     * @return array
     */
    public function getCurrentWeather(string $location, array $options = []): array
    {
        // výchozí možnosti
        $defaultOptions = [
            'unitGroup' => 'metric',    // metric/us/uk
            'lang' => 'en',             // jazyk popisů
            'include' => 'days',        // která data zahrnout
        ];

        // sloučení výchozích options a uživatelských options (uživatelské mají přednost)
        $mergedOption = array_merge($defaultOptions, $options);

        // Vytvoří klíče pro cache, zahrnuje všechny parametry
        $cacheKey = md5($location . serialize($mergedOption));

        // Pokus o načtění z cache
        if ($this->cache !== null) {
            $data = $this->cache->load($cacheKey);
            if ($data !== null) {
                return $data;
            }
        }

        // Sestavení URL s lokací
        $url = self::BASE_URL . urlencode($location) . '/today';

        // Volání API, ted i s možnostmi
        $response = $this->makeRequest($url, $mergedOption);

        // Uložení do cache, pokud je dostupná
        if ($this->cache !== null) {
            $dependencies = [
                Cache::Expire => '30 minutes',
            ];
            $this->cache->save($cacheKey, $response, $dependencies);
        }

        return $response;
    }

    /**
     * Získání předpovědi pro lokaci včetně možností
     *
     * @param string $location Lokace (město, PSČ, souřadnice ...)
     * @param int $days Počet dní pro předpověď
     * @param array $options Doplňující nastavení
     * @return array
     */
    public function getForecast(string $location, int $days = 5, array $options = []): array
    {
        $defaultOptions =[
            'unitGroup' => 'metric',    // metric/us/uk
            'lang' => 'en',             // jazyk popisů
            'include' => 'days',        // která data zahrnout
        ];

        // Sloučí výchozí a uživatelem zadané možnosti
        $mergedOptions = array_merge($defaultOptions, $options);

        // Vytvoří klíč pro cache
        $cacheKey = md5($location . '_' . $days . serialize($mergedOptions));

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

        // Volání API + ted s možnostmi
        $response = $this->makeRequest($url, $mergedOptions);

        // ulozi do cache pokud ji ma
        if ($this->cache !== null) {
            $dependencies = [
                Cache::Expire => '2 hours',
            ];
            $this->cache->save($cacheKey, $response, $dependencies);
        }

        return $response;
    }

    /**
     * @param string $url       API endpoint url
     * @param array $options    Možnosti požadavku
     * @return array
     * @throws \Exception
     */
    private function makeRequest(string $url, array $options = []): array
    {
        try {
            $params = [
                'query' => array_merge([
                    'key' => $this->apiKey,     // API klíč
                    'contentType' => 'json',    // Formát odpovědi - JSON
                ], $options),   // přidání uživ. možností (tam jsou dny, jednotky)
            ];

            // Provede HTTP požadavek
            $response = $this->httpClient->get($url, $params);

            // Dekoduj JSON odpověd
            $data = Json::decode((string) $response->getBody(), forceArrays: true);

            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Zachyceni a zpracování chyb HTTP požadavku
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = (string) $e->getResponse()->getBody();

                throw new \Exception("API request failed with status code $statusCode: $errorBody", $statusCode);
            }

            throw new \Exception("API request failed: " . $e->getMessage(), 500);
        } catch (\Exception $e) {
            // Zachycení obecných chyb
            throw new \Exception("Failed to process API response: " .$e->getMessage(), 500);
        }
    }
}