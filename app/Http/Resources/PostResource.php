<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'website_id' => $this->website_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'website' => new WebsiteResource($this->whenLoaded('website')),
        ];
    }
}
