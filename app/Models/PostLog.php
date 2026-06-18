<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'platform_id',
        'action',
        'status',
        'message',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    /**
     * Get the post this log belongs to
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the platform this log is related to
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
