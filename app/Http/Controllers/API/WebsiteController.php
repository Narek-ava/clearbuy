<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Website\GetRequest;
use App\Http\Resources\WebsiteResource;
use App\Services\WebsiteService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WebsiteController extends Controller
{
    /**
     * @var WebsiteService
     */
    private $websiteService;

    /**
     * @param WebsiteService $websiteService
     */
    public function __construct(WebsiteService $websiteService)
    {
        $this->websiteService = $websiteService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function get(): AnonymousResourceCollection
    {
        return WebsiteResource::collection($this->websiteService->get());
    }
}
