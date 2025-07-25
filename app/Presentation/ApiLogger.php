<?php
declare(strict_types=1);

namespace App\Presentation;

class ApiLogger
{
    /**
     * Cesta k log adresáři
     */
    private string $logDir;


    public function __construct(string $logDir = null)
    {
        // pokud cesta není zadána, použij výchozí
        $this->logDir = $logDir ?? __DIR__ . '/../../log';

        // Kontrola, že adresář existuje
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    /**
     * Zaloguje API požadavek
     *
     * @param string $endpoint Endpoint API (momentalne 'current', 'forecast')
     * @param array $params Parametry požadavku
     * @param string $clientIp IP adresa odesílatele požadavku
     * @param string $method HTTP metoda (GET, POST...)
     * @param string $url Kompletní URL požadavku
     * @param int $statusCode HTTP status kod odpovědi
     * @param float $duration Doba trvání požadavku (v sekundách)
     * @param string|null $userAgent User-Agent string klienta (prohlížec, aplikace)
     * @return void
     */
    public function logRequest(
        string $endpoint,
        array $params,
        string $clientIp,
        string $method,
        string $url,
        int $statusCode,
        float $duration,
        ?string $userAgent = null,
    ): void {
        // Vytvoření log záznamu
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'endpoint' => $endpoint,
            'method' => $method,
            'url' => $url,
            'params' => $params,
            'client_ip' => $clientIp,
            'status_code' => $statusCode,
            'duration' => round($duration, 4),  // Round na 4 desetinna místa
            'user_agent' => $userAgent ?? 'unknown',
        ];

        // Jméno log souboru bude podle datumu
        $logFile = $this->logDir . '/api_' . date('Y-m-d') . '.log';

        // Zápis do souboru
        $logLine = json_encode($logEntry) . PHP_EOL;

        try {
            file_put_contents($logFile, $logLine, FILE_APPEND);
        } catch (\Exception $e) {
            // Tiché selhání v případě problému
        }
    }

    /**
     * @param string $endpoint Endpoint API (momentalne 'current', 'forecast')
     * @param array $params Parametry požadavku
     * @param string $clientIp IP adresa odesílatele požadavku
     * @param string $method HTTP metoda (GET, POST...)
     * @param string $url Kompletní URL požadavku
     * @param string $errorMessage Chybová zpráva
     * @param string|null $userAgent User-Agent string klienta (prohlížec, aplikace)
     * @return void
     */
    public function logError(
        string $endpoint,
        array $params,
        string $clientIp,
        string $method,
        string $url,
        string $errorMessage,
        ?string $userAgent = null,
    ): void {
        // Vytvoření log záznamu
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'endpoint' => $endpoint,
            'method' => $method,
            'url' => $url,
            'params' => $params,
            'client_ip' => $clientIp,
            'error' => $errorMessage,
            'user_agent' => $userAgent ?? 'unknown',
        ];

        // Jméno log souboru bude podle datumu
        $logFile = $this->logDir . '/api_errors_' . date('Y-m-d') . '.log';

        // zápis do souboru
        $logLine = json_encode($logEntry) . PHP_EOL;

        try {
        file_put_contents($logFile, $logLine, FILE_APPEND);
        } catch (\Exception $e) {
            // Tiché selhání
        }
    }
}