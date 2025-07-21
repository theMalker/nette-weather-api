<?php
declare(strict_types=1);

namespace App\Presentation;

trait ApiResponseTrait
{
    protected ApiResponse $apiResponse;

    public function injectApiResponse(ApiResponse $apiResponse): void
    {
        $this->apiResponse = $apiResponse;
    }
}