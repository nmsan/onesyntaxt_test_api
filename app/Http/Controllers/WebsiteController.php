<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $website = Website::create([
                'name' => $request->name,
                'url' => $request->url,
                'user_id' => $request->user_id
            ]);

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
