<?php

namespace App\Http\Controllers;

use App\Contracts\WebsiteCreationInterface;
use App\Contracts\WebsiteRetrievalInterface;
use App\Http\Requests\CreateWebsiteRequest;
use App\Http\Resources\WebsiteResource;

class WebsiteController extends Controller
{
    private WebsiteCreationInterface $websiteCreationService;
    private WebsiteRetrievalInterface $websiteRetrievalService;

    public function __construct(
        WebsiteCreationInterface $websiteCreationService,
        WebsiteRetrievalInterface $websiteRetrievalService
    ) {
        $this->websiteCreationService = $websiteCreationService;
        $this->websiteRetrievalService = $websiteRetrievalService;
    }

    public function index()
    {
        try {
            $websites = $this->websiteRetrievalService->getAllWebsites();

            return response()->json([
                'message' => 'Websites retrieved successfully',
                'data' => WebsiteResource::collection($websites)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve websites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateWebsiteRequest $request)
    {
        try {
            $website = $this->websiteCreationService->createWebsite($request->validated());

            return response()->json([
                'message' => 'Website created successfully',
                'data' => new WebsiteResource($website)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Website creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
