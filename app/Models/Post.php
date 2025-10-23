<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'status',
        'website_id',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
