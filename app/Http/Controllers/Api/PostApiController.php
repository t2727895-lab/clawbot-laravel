<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Platform;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PostApiController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            // Validate required and optional fields
            $validated = $request->validate([
                'content' => 'required|string|min:10',
                'image_url' => 'nullable|url',
                'scheduled_at' => 'nullable|string',
            ]);

            // Parse and validate scheduled_at if provided
            $scheduledAt = null;
            if (isset($validated['scheduled_at']) && !empty($validated['scheduled_at'])) {
                try {
                    // Try to parse the scheduled_at string
                    $scheduledAt = Carbon::parse($validated['scheduled_at']);
                    
                    // Validate that the scheduled date is in the future
                    if ($scheduledAt->isPast()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validation failed',
                            'errors' => [
                                'scheduled_at' => ['Scheduled date must be in the future']
                            ],
                        ], 422);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'scheduled_at' => ['Invalid date format. Use ISO 8601 format (e.g., 2026-06-18T14:30:00Z or 2026-06-18 14:30:00)']
                        ],
                    ], 422);
                }
            }

            // Create the post with validated data
            $post = Post::create([
                'content' => $validated['content'],
                'image' => $validated['image_url'] ?? null,
                'status' => 'pending_approval', // Default status
                'scheduled_at' => $scheduledAt,
                'published_at' => null,
            ]);

            // Attach all active platforms by default
            $activePlatforms = Platform::where('is_active', true)->pluck('id')->toArray();
            
            if (!empty($activePlatforms)) {
                $post->platforms()->attach($activePlatforms);
            }

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => [
                    'id' => $post->id,
                    'status' => $post->status,
                    'scheduled_at' => $post->scheduled_at ? $post->scheduled_at->toIso8601String() : null,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
