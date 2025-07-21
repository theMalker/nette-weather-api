<?php
declare(strict_types=1);

namespace Tests\Model;

use App\Model\WeatherFormatter;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Testy pro WeatherFormatter
 */
class WeatherFormatterTest extends TestCase
{
    private WeatherFormatter $formatter;

    public function setUp(): void
    {
        // Inicializace před každým testem
        $this->formatter = new WeatherFormatter();
    }

    /**
     * Test pro metodu formatCurrentWeather
     *
     * @return void
     */
    public function testFormatCurrentWeather(): void
    {
        // testovací data - příklad odpovědi z API
        $apiResponse = [
            'resolvedAddress' => 'Prague, Czech Republic',
            'latitude' => 50.0755,
            'longitude' => 14.4378,
            'timezone' => 'Europe/Prague',
            'days' => [
                [
                    'datetime' => '2023-07-21',
                    'temp' => 25.5,
                    'feelslike' => 26.2,
                    'humidity' => 65,
                    'windspeed' => 12.3,
                    'winddir' => 180,
                    'conditions' => 'Partly Cloudy',
                    'description' => 'Partly cloudy throughout the day.',
                    'precip' => 0.5,
                    'icon' => 'partly-cloudy-day'
                ]
            ]
        ];

        // volání testované metody
        $result = $this->formatter->formatCurrentWeather($apiResponse);

        // ověření výsledku
        Assert::same('Prague, Czech Republic', $result['location']['name']);
        Assert::same(50.0755, $result['location']['latitude']);
        Assert::same(14.4378, $result['location']['longitude']);
        Assert::same('Europe/Prague', $result['location']['timezone']);

        Assert::same(25.5, $result['current']['temperature']);
        Assert::same(26.2, $result['current']['feels_like']);
        Assert::same(65, $result['current']['humidity']);
        Assert::same('Partly Cloudy', $result['current']['conditions']);
        Assert::same('partly-cloudy-day', $result['current']['icon']);
    }

    /**
     * Test pro metodu formatForecast
     *
     * @return void
     */
    public function testFormatForecast(): void
    {
        // testovací data - příklad API odpovědi pro předpověd
        $apiResponse = [
            'resolvedAddress' => 'Prague, Czech Republic',
            'latitude' => 50.0755,
            'longitude' => 14.4378,
            'timezone' => 'Europe/Prague',
            'days' => [
                [
                    'datetime' => '2023-07-21',
                    'tempmax' => 28.5,
                    'tempmin' => 18.2,
                    'feelslike' => 26.2,
                    'humidity' => 65,
                    'windspeed' => 12.3,
                    'winddir' => 180,
                    'conditions' => 'Partly Cloudy',
                    'description' => 'Partly cloudy throughout the day.',
                    'precip' => 0.5,
                    'precipprob' => 30,
                    'icon' => 'partly-cloudy-day'
                ],
                [
                    'datetime' => '2023-07-22',
                    'tempmax' => 30.0,
                    'tempmin' => 19.5,
                    'feelslike' => 31.2,
                    'humidity' => 60,
                    'windspeed' => 10.1,
                    'winddir' => 200,
                    'conditions' => 'Clear',
                    'description' => 'Clear conditions all day.',
                    'precip' => 0.0,
                    'precipprob' => 0,
                    'icon' => 'clear-day'
                ]
            ]
        ];

        // Volání testované metody
        $result = $this->formatter->formatForecast($apiResponse, 2);

        // Ověření výsledku
        Assert::same('Prague, Czech Republic', $result['location']['name']);
        Assert::count(2, $result['forecast']);

        // Ověření prvního dne předpovědi
        Assert::same('2023-07-21', $result['forecast'][0]['datetime']);
        Assert::same(28.5, $result['forecast'][0]['temp_max']);
        Assert::same(18.2, $result['forecast'][0]['temp_min']);
        Assert::same('Partly Cloudy', $result['forecast'][0]['conditions']);

        // Ověření druhého dne předpovědi
        Assert::same('2023-07-22', $result['forecast'][1]['datetime']);
        Assert::same(30.0, $result['forecast'][1]['temp_max']);
        Assert::same(19.5, $result['forecast'][1]['temp_min']);
        Assert::same('Clear', $result['forecast'][1]['conditions']);
    }

    /**
     * Test pro limit dní v předpovšdi
     *
     * @return void
     */
    public function testFormatForecastWithLimitedDays(): void
    {
        // Testovací data s více dny, než požadujeme
        $apiResponse = [
            'resolvedAddress' => 'Prague, Czech Republic',
            'days' => [
                ['datetime' => '2023-07-21'],
                ['datetime' => '2023-07-22'],
                ['datetime' => '2023-07-23'],
                ['datetime' => '2023-07-24'],
                ['datetime' => '2023-07-25'],
            ]
        ];

        // požadujeme jen 3 dny
        $result = $this->formatter->formatForecast($apiResponse, 3);

        // Oveření že jsme dostali jen 3 dny
        Assert::count(3, $result['forecast']);
        Assert::same('2023-07-21', $result['forecast'][0]['datetime']);
        Assert::same('2023-07-22', $result['forecast'][1]['datetime']);
        Assert::same('2023-07-23', $result['forecast'][2]['datetime']);
    }
}

// spuštění testů
(new WeatherFormatterTest())->run();