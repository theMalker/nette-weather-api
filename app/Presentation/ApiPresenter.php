<?php

declare(strict_types=1);

namespace App\Presentation;



use App\Model\WeatherFormatter;
use App\Model\WeatherService;
use App\Model\RateLimiter;
use Nette\Application\UI\Presenter;


final class ApiPresenter extends Presenter
{
    use ApiResponseTrait;
    use ApiLoggerTrait;

    /**
     * Konstanty RateLimiteru
     */
    private const RATE_LIMIT = 60;  // 60 požadavku
    private const RATE_WINDOW = 60; // na 60s (1 minuta)

    private WeatherService $weatherService;
    private WeatherFormatter $formatter;
    private RateLimiter $rateLimiter;

    public function __construct(
        WeatherService $weatherService,
        WeatherFormatter $formatter,
        RateLimiter $rateLimiter
    ) {
        parent::__construct();
        $this->weatherService = $weatherService;
        $this->formatter = $formatter;
        $this->rateLimiter = $rateLimiter;
    }

    protected function startup(): void
    {
        parent::startup();

        //CORS hlavičky pro API
        $this->setCorsHeaders();

        // Při startu získáme IP a kontrolujeme limity
        $clientIp = $this->getHttpRequest()->getRemoteAddress();
        $isAllowed = $this->rateLimiter->check($clientIp, self::RATE_LIMIT, self::RATE_WINDOW);

        // Doplnění hlavičky s rateLimit info
        $rateLimitInfo = $this->rateLimiter->getInfo($clientIp, self::RATE_LIMIT, self::RATE_WINDOW);
        $this->getHttpResponse()->addHeader('X-RateLimit-Limit', (string) $rateLimitInfo['limit']);
        $this->getHttpResponse()->addHeader('X-RateLimit-Remaining', (string) $rateLimitInfo['remaining']);
        $this->getHttpResponse()->addHeader('X-RateLimit-Reset', (string) $rateLimitInfo['reset']);

        // Při překročení limitu vracíme chybu 429 - Too Many Requests
        if (!$isAllowed) {
            $retryAfter = max(1, $rateLimitInfo['reset'] - time());
            $this->getHttpResponse()->addHeader('Retry-After', (string) $retryAfter);
            $this->apiResponse->sendError('Rate limit exceeded. Try again later.', 429);
        }

    }

    /**
     * API endpoint pro aktuální počasí
     *
     * @param string $location  Lokace (město, PSČ, souřadnice ..)
     * @param string $units     Jednotky (metric/us/uk)
     * @param string $lang      Jazyk (default en)
     * @return void
     */
    public function actionCurrent(string $location, string $units = 'metric', string $lang = 'en'): void
    {
        try {
            if (empty($location)) {
                $this->apiResponse->sendError('Location parameter is required', 400);
            }

            // Validace jednotek
            $validUnits = ['metric', 'us', 'uk'];
            if (!in_array($units, $validUnits)) {
                $this->apiResponse->sendError('Invalid units. Valid values: ' . implode(', ', $validUnits), 400);
            }

            // Seskupíme options pro API request
            $options = [
                'unitGroup' => $units,
                'lang' => $lang,
            ];

            // Volání služby + options
            $data = $this->weatherService->getCurrentWeather($location, $options);

            // Formátování odpovědi
            $formattedData = $this->formatter->formatCurrentWeather($data);

            // Log úspěšné odpovědi
            $this->logApiRequest('current', [
                'location' => $location,
                'units' => $units,
                'lang' => $lang
            ]);

            // Odešle odpověd
            $this->apiResponse->sendSuccess($formattedData);
        } catch (\Exception $e) {
            // logování chyby
            $this->logApiError('current', [
                'location' => $location,
                'units' => $units,
                'lang' => $lang
            ], $e->getMessage());

            $this->apiResponse->sendError($e->getMessage());
        }
    }


    public function actionForecast(string $location, int $days = 5, string $units = 'metric', string $lang = 'en'): void
    {
        try {
            // zakladní validace vstupu
            if (empty($location)) {
                $this->apiResponse->sendError('Location parameter is required', 400);
            }
            if ($days < 1 || $days > 7) {
                $this->apiResponse->sendError('Days must be between 1 and 7 days', 400);
            }

            // Validace jednotek
            $validUnits = ['metric', 'us', 'uk'];
            if (!in_array($units, $validUnits)) {
                $this->apiResponse->sendError('Invalid units. Valid values: ' . implode(', ', $validUnits), 400);
            }

            // Seskupíme options pro API request
            $options = [
                'unitGroup' => $units,
                'lang' => $lang,
            ];

            $data = $this->weatherService->getForecast($location, $days, $options);

            // Formátování odpovědi
            $formattedData = $this->formatter->formatForecast($data, $days);

            // Logování úspěšného požadavku
            $this->logApiRequest('forecast', [
                'location' => $location,
                'days' => $days,
                'units' => $units,
                'lang' => $lang
            ]);

            $this->apiResponse->sendSuccess($formattedData);
        } catch (\Exception $e) {
            // Logování chyby
            $this->logApiError('forecast', [
                'location' => $location,
                'days' => $days,
                'units' => $units,
                'lang' => $lang
            ], $e->getMessage());

            $this->apiResponse->sendError($e->getMessage());
        }
    }

    /**
     * Pomocná metoda CORS
     * Startup je čistější
     * Mohu použít i jinde
     * Snaddnější maintenance
     *
     * @return void
     */
    private function setCorsHeaders(): void
    {
        $this->getHttpResponse()->addHeader('Access-Control-Allow-Origin', '*');
        $this->getHttpResponse()->addHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $this->getHttpResponse()->addHeader('Access-Control-Allow-Headers', 'Content-Type');
    }
}