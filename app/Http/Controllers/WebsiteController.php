<?php

namespace App\Http\Controllers;

use App\Contracts\WebsiteServiceInterface;
use App\Http\Requests\CreateWebsiteRequest;

class WebsiteController extends Controller
{
    private WebsiteServiceInterface $websiteService;

    public function __construct(WebsiteServiceInterface $websiteService)
    {
        $this->websiteService = $websiteService;
    }

    public function store(CreateWebsiteRequest $request)
    {
        try {
            $website = $this->websiteService->createWebsite($request->validated());

            return response()->json([
                'message' => 'Website created successfully',
                'data' => $website
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Website creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
