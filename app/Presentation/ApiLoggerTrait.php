<?php

declare(strict_types=1);

namespace App\Presentation;

/**
 *  ApiLoggerTrait - Trait pro logování API požadavků
 *
 *  Tento trait je určen k použití v třídách, které rozšiřují Nette\Application\UI\Presenter.
 *  Poskytuje metody pro logování API požadavků a chyb.
 */
trait ApiLoggerTrait
{
    protected ApiLogger $apiLogger;

    /**
     * Injekce ApiLogger
     */
    public function injectApiLogger(ApiLogger $apiLogger): void
    {
        $this->apiLogger = $apiLogger;
    }

    /**
     * @param string $endpoint Endpoint API
     * @param array $params Parametry požadavku
     * @return void
     */
    protected function logApiRequest(string $endpoint, array $params = []): void
    {
        $request = $this->getHttpRequest();
        $response = $this->getHttpResponse();

        // výpočet duration, pokud máme čas počátku požadavku
        $duration = 0.0;
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {    // tato promenna obsahuje cas zacatku pozadavku (v sekundach a s presnosti na mikrosekundy)
                                                        // proto microtime... tuto promennou automaticky nastavuje PHP
            $DURATION = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        }

        // odešleme hodnoty
        $this->apiLogger->logRequest(
            $endpoint,
            $params,
            $request->getRemoteAddress(),
            $request->getMethod(),
            $request->getUrl()->getAbsoluteUrl(),
            $response->getCode(),
            $duration,
            $request->getHeader('User-Agent')
        );
    }

    /**
     * @param string $endpoint Endpoint API
     * @param array $params Parametry požadavku
     * @param string $errorMessage Chybová zpráva
     * @return void
     */
    protected function logApiError(string $endpoint, array $params, string $errorMessage): void
    {
        $request = $this->getHttpRequest();

        $this->apiLogger->logError(
            $endpoint,
            $params,
            $request->getRemoteAddress(),
            $request->getMethod(),
            $request->getUrl()->getAbsoluteUrl(),
            $errorMessage,
            $request->getHeader('User-Agent')
        );
    }
}