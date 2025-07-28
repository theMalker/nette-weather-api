# Nette Weather API Wrapper

A PHP wrapper for the Visual Crossing Weather API built with Nette Framework. This project provides a simple and efficient way to access weather data with additional features like caching, rate limiting, and comprehensive logging.

## Features

- **Current Weather Data** - Get current weather conditions for any location
- **Weather Forecast** - Get weather forecast for up to 7 days
- **Response Formatting** - Clean, consistent response format
- **Caching** - Reduce API calls with built-in caching
- **Rate Limiting** - Protect your API from overuse
- **Logging** - Track API usage and errors
- **Customizable Parameters** - Support for units, language, and other parameters

## Requirements

- PHP 8.0 or higher
- Composer
- Web server with URL rewriting (Apache, Nginx)
- Visual Crossing API key (get it [here](https://www.visualcrossing.com/weather-api))

## Installation

1. **Clone the repository**

```bash
git clone https://github.com/themalker/nette-weather-api.git
cd nette-weather-api
```

2. **Install dependencies**

```bash
composer install
```

3. **Set up environment variables**

Create a `.env` file in the root directory:

```
VISUALCROSSING_API_KEY=your_api_key_here
APP_DEBUG=true
```

4. **Configure web server**

For Apache, ensure the `.htaccess` files are properly set up and mod_rewrite is enabled.

Example Apache VirtualHost configuration:

```apache
<VirtualHost *:80>
    ServerName weather-api.localhost
    DocumentRoot /path/to/nette-weather-api/www
    
    <Directory /path/to/nette-weather-api/www>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

5. **Verify installation**

Navigate to `http://localhost/nette-weather-api/www/` (or your configured domain) in your browser. You should see the API documentation page.

## Usage

### API Endpoints

The API provides the following endpoints:

#### Get Current Weather

```
GET /api/current/{location}
```

Parameters:
- `location` (required) - City name, postal code, or coordinates
- `units` (optional) - Units of measurement: `metric` (default), `us`, `uk`
- `lang` (optional) - Language for text descriptions: `en` (default), `cs`, `de`, etc.

Example request:
```
http://localhost/nette-weather-api/api/current/Prague?units=metric&lang=en
```

Example response:
```json
{
    "status": "success",
    "message": "Success",
    "data": {
        "location": {
            "name": "Prague, Czech Republic",
            "latitude": 50.0755,
            "longitude": 14.4378,
            "timezone": "Europe/Prague"
        },
        "current": {
            "temperature": 23.5,
            "feels_like": 24.2,
            "humidity": 65,
            "wind_speed": 12.3,
            "wind_direction": 180,
            "conditions": "Partly Cloudy",
            "description": "Partly cloudy throughout the day.",
            "precipitation": 0.5,
            "icon": "partly-cloudy-day"
        },
        "forecast_source": "Visual Crossing Weather API",
        "updated_at": "2023-07-25 15:30:45"
    }
}
```

#### Get Weather Forecast

```
GET /api/forecast/{location}/{days}
```

Parameters:
- `location` (required) - City name, postal code, or coordinates
- `days` (optional) - Number of days for forecast (1-7, default: 5)
- `units` (optional) - Units of measurement: `metric` (default), `us`, `uk`
- `lang` (optional) - Language for text descriptions: `en` (default), `cs`, `de`, etc.

Example request:
```
http://localhost/nette-weather-api/api/forecast/Prague/3?units=metric&lang=en
```

Example response:
```json
{
    "status": "success",
    "message": "Success",
    "data": {
        "location": {
            "name": "Prague, Czech Republic",
            "latitude": 50.0755,
            "longitude": 14.4378,
            "timezone": "Europe/Prague"
        },
        "forecast": [
            {
                "datetime": "2023-07-25",
                "temp_max": 25.8,
                "temp_min": 18.2,
                "feels_like": 26.2,
                "humidity": 65,
                "wind_speed": 12.3,
                "wind_direction": 180,
                "conditions": "Partly Cloudy",
                "description": "Partly cloudy throughout the day.",
                "precipitation": 0.5,
                "precipitation_probability": 30,
                "icon": "partly-cloudy-day"
            },
            {
                "datetime": "2023-07-26",
                "temp_max": 24.5,
                "temp_min": 17.8,
                "feels_like": 25.0,
                "humidity": 60,
                "wind_speed": 10.1,
                "wind_direction": 200,
                "conditions": "Clear",
                "description": "Clear conditions all day.",
                "precipitation": 0.0,
                "precipitation_probability": 0,
                "icon": "clear-day"
            },
            {
                "datetime": "2023-07-27",
                "temp_max": 26.2,
                "temp_min": 19.5,
                "feels_like": 27.8,
                "humidity": 55,
                "wind_speed": 8.7,
                "wind_direction": 220,
                "conditions": "Clear",
                "description": "Clear conditions all day.",
                "precipitation": 0.0,
                "precipitation_probability": 0,
                "icon": "clear-day"
            }
        ],
        "forecast_source": "Visual Crossing Weather API",
        "updated_at": "2023-07-25 15:32:10"
    }
}
```

### Rate Limiting

The API implements rate limiting to ensure fair usage. By default, it allows 60 requests per minute per IP address. When the limit is exceeded, the API returns a 429 Too Many Requests response.

Rate limit headers:
- `X-RateLimit-Limit` - Maximum requests allowed per time window
- `X-RateLimit-Remaining` - Remaining requests in the current time window
- `X-RateLimit-Reset` - Time when the rate limit resets (Unix timestamp)

### Error Handling

In case of an error, the API returns a JSON response with status code 4xx or 5xx:

```json
{
    "status": "error",
    "message": "Error description"
}
```

Common error codes:
- `400 Bad Request` - Invalid parameters
- `429 Too Many Requests` - Rate limit exceeded
- `500 Internal Server Error` - Server-side error

## Using the API in Your Code

### PHP Example

```php
<?php
// Get current weather
$response = file_get_contents('http://localhost/nette-weather-api/api/current/Prague');
$data = json_decode($response, true);

if ($data['status'] === 'success') {
    echo "Current temperature in Prague: " . $data['data']['current']['temperature'] . "°C\n";
    echo "Conditions: " . $data['data']['current']['conditions'] . "\n";
}
```

### JavaScript Example

```javascript
// Get weather forecast
fetch('http://localhost/nette-weather-api/api/forecast/Prague/3')
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      console.log('Forecast for Prague:');
      data.data.forecast.forEach(day => {
        console.log(`${day.datetime}: ${day.temp_min}°C - ${day.temp_max}°C, ${day.conditions}`);
      });
    }
  });
```

## Configuration

### Cache Settings

The application uses server-side caching to improve performance and reduce calls to the external API. Cache configuration can be modified in `app/Model/WeatherService.php`:

- Current weather data is cached for 30 minutes
- Forecast data is cached for 1 hour

### Rate Limiter Settings

Rate limiting configuration can be found in `app/Presentation/ApiPresenter.php`:

```php
private const RATE_LIMIT = 60;   // 60 requests
private const RATE_WINDOW = 60;  // per 60 seconds (1 minute)
```

### Logging

API logs are stored in the `log` directory:
- `api_YYYY-MM-DD.log` - Successful API requests
- `api_errors_YYYY-MM-DD.log` - API errors

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Nette Framework](https://nette.org/)
- [Visual Crossing Weather API](https://www.visualcrossing.com/weather-api)
- [GuzzleHTTP](https://docs.guzzlephp.org/)
