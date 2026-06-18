<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const STATUSES = ['pending_approval', 'queued', 'rejected', 'published', 'failed'];

    protected $fillable = [
        'content',
        'image',
        'status',
        'scheduled_at',
        'published_at',
        'clawbot_post_id',
        'rejection_reason',
        'source',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the platforms for this post
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'post_platforms');
    }

    /**
     * Get platform names as array
     */
    public function getPlatformNames()
    {
        return $this->platforms()->pluck('name')->toArray();
    }

    /**
     * Get logs for this post
     */
    public function logs()
    {
        return $this->hasMany(PostLog::class)->latest();
    }

    /**
     * Get the user who approved this post
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: posts that are approved (queued) and due to be published now
     */
    public function scopeDueForPublishing($query)
    {
        return $query->where('status', 'queued')->where('scheduled_at', '<=', now());
    }
}
