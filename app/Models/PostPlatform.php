<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPlatform extends Model
{
    use HasFactory;

    protected $table = 'post_platforms';

    protected $fillable = [
        'post_id',
        'platform_id',
    ];

    /**
     * Get the post this platform entry belongs to
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the platform
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
