<?php
declare(strict_types=1);

namespace App\Presentation;

class ApiResponse
{

    /**
     * Odešle data jako JSON odpověď
     *
     * @param array $data
     * @param int $httpCode
     * @return void
     */
    public function sendAsJson(array $data, int $httpCode = 200): void
    {
        // zastavíme výstup bufferingu
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Nastavení headers
        if (!headers_sent()) {
            http_response_code($httpCode);
            header('Content-Type: application/json');
            header('Cache-Control: no-cache');
            header('Connection: close');
        }

        //Pošleme JSON
        echo json_encode($data);

        // Flush a exit
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
        }

        die();
    }

    /**
     * Pošle chybovou odpověď
     *
     * @param string $message
     * @param int $httpCode
     * @return void
     */
    public function sendError(string $message, int $httpCode = 400): void
    {
        $this->sendAsJson([
            'status' => 'error',
            'message' => $message,
        ], $httpCode);
    }

    /**
     * Odešle úspěšnou odpověď
     *
     * @param array $data
     * @param string $message
     * @return void
     */
    public function sendSuccess(array $data = [], string $message = 'Success'): void
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
        $this->sendAsJson($response);
    }

    /**
     * Metoda pro zpracování dat z požadavku
     *
     * @return array
     */
    public function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        if ($input && $decoded === null) {
            $this->sendError('Invalid JSON format', 400);
        }
        return $decoded ?: [];
    }
}