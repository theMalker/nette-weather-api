<?php

declare(strict_types=1);

namespace App\Presentation;



use App\Model\WeatherFormatter;
use App\Model\WeatherService;
use Nette\Application\UI\Presenter;


final class ApiPresenter extends Presenter
{
    private WeatherService $weatherService;
    private WeatherFormatter $formatter;
    use ApiResponseTrait;

    public function __construct(
        WeatherService $weatherService,
        WeatherFormatter $formatter
    ) {
        parent::__construct();
        $this->weatherService = $weatherService;
        $this->formatter = $formatter;
    }

    protected function startup(): void
    {
        parent::startup();

        //CORS hlavičky pro API
        $this->setCorsHeaders();
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

            // Odešle odpověd
            $this->apiResponse->sendSuccess($formattedData);
        } catch (\Exception $e) {
            $this->apiResponse->sendError($e->getMessage());
        }
    }


    public function actionForecast(string $location, int $days = 5): void
    {
        try {
            // zakladní validace vstupu
            if (empty($location)) {
                $this->apiResponse->sendError('Location parameter is required', 400);
            }
            if ($days < 1 || $days > 7) {
                $this->apiResponse->sendError('Days must be between 1 and 7 days', 400);
            }

            $data = $this->weatherService->getForecast($location, $days);
            $this->apiResponse->sendSuccess($data);
        } catch (\Exception $e) {
            $this->apiResponse->sendError($e->getMessage());
        }
    }



    /**
     * Metoda pro zpracování dat z požadavku
     *
     * @return array
     */
    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if ($input && $decoded === null) {
            $this->apiResponse->sendError('Invalid JSON format', 400);
        }
        return $decoded ?: [];
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