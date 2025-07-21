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

        $this->template->endpoints = [
            [
                'name' => 'Current Weather',
                'url' => 'api/current/{location}',
                'method' => 'GET',
                'description' => 'Get current weather for a specific location',
                'parameters' => [
                    'location' => 'City name, postal code, or coordinates (required)'
                ],
                'example' => 'api/current/Prague'
            ],
            [
                'name' => 'Weather Forecast',
                'url' => 'api/forecast/{location}/{days}',
                'method' => 'GET',
                'description' => 'Get forecast for a specific location',
                'parameters' => [
                    'location' => 'City name, postal code, or coordinates (required)'
                ],
                'example' => 'api/forecast/Prague/3'
            ]
        ];
    }
}